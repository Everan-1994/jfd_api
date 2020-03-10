<?php

namespace App\Http\Controllers\Api;

use App\Models\HouseType;
use App\Models\Images;
use App\Models\Tag;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;
use App\Http\Resources\ArticleResource;
use Illuminate\Support\Facades\Storage;

class ArticlesController extends Controller
{
    protected $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function index(Request $request)
    {
        $identify = \Auth::user()->identify;
        $article = $this->article->when(isset($request->status), function ($query) use ($request) {
            return $query->whereStatus($request->status);
        })
            ->when($identify === 3, function ($query) {
                return $query->where('status', 1);
            })
            ->when(isset($request->title), function ($query) use ($request) {
                return $query->where('title', 'like', '%' . $request->title . '%');
            })
            ->orderBy($request->order ?: 'created_at', $request->sort ?: 'desc')
            ->paginate($request->pageSize, ['*'], 'page', $request->page ?: 1);

        return ArticleResource::collection($article);
    }

    public function show(Article $article)
    {
        return new ArticleResource($article);
    }

    public function views(Request $request)
    {
        $this->article->whereId($request->id)->increment('views', 1, ['true_views' => \DB::raw('true_views  + 1')]);

        return response([
            'code' => 0,
            'msg'  => 'Success'
        ]);
    }

    public function store(ArticleRequest $articleRequest, Tag $tags, HouseType $houseType)
    {
        \DB::beginTransaction();
        try {
            $article = $this->article->create([
                'title'       => $articleRequest->title,
                'subtitle'    => $articleRequest->subtitle,
                'phone'       => $articleRequest->phone,
                'up_body'     => $articleRequest->up_body,
                'down_body'   => $articleRequest->down_body,
                'views'       => $articleRequest->views,
                'asks'        => $articleRequest->asks,
                'author_id'   => \Auth::id(),
                'status'      => $articleRequest->status
            ]);

            // 标签
            $tag = explode('&', $articleRequest->tags);
            $data = [];
            foreach ($tag as $k => $val) {
                $data[$k] = [
                    'article_id' => $article['id'],
                    'name'       => $val,
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString()
                ];
            }
            $tags->insert($data);

            // 户型
            $house_type = explode('&', $articleRequest->house_type);
            $ht = [];
            foreach ($house_type as $key => $value) {
                $ht[$key] = [
                    'article_id' => $article['id'],
                    'name'       => $value,
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString()
                ];
            }
            $houseType->insert($ht);

            // 文章图片入库
            if ($articleRequest->images) {
                // 图片本地保存
                $this->add_images($articleRequest->images, $article['id']);
            }

            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollBack();
            return response(['msg' => $exception->getMessage()], 400);
        }

        return new ArticleResource($article);
    }

    public function update(ArticleRequest $articleRequest, Tag $tags, HouseType $houseType)
    {
        \DB::beginTransaction();
        try {
            $this->article->whereId($articleRequest->id)
                ->update([
                    'title'       => $articleRequest->title,
                    'subtitle'    => $articleRequest->subtitle,
                    'phone'       => $articleRequest->phone,
                    'up_body'     => $articleRequest->up_body,
                    'down_body'   => $articleRequest->down_body,
                    'views'       => $articleRequest->views,
                    'asks'        => $articleRequest->asks,
                    'status'      => $articleRequest->status
                ]);

            // 先删除原有标签
            $tags->whereArticleId($articleRequest->id)->delete();

            $tag = explode('&', $articleRequest->tags);
            $data = [];
            foreach ($tag as $k => $val) {
                $data[$k] = [
                    'article_id' => $articleRequest->id,
                    'name'       => $val,
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString()
                ];
            }

            $tags->insert($data);

            // 先删除原有户型
            $houseType->whereArticleId($articleRequest->id)->delete();

            $house_type = explode('&', $articleRequest->house_type);
            $ht = [];
            foreach ($house_type as $key => $value) {
                $ht[$key] = [
                    'article_id' => $articleRequest->id,
                    'name'       => $value,
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString()
                ];
            }
            $houseType->insert($ht);

            if ($articleRequest->images) {
                // 图片本地保存
                $this->add_images($articleRequest->images, $articleRequest->id);
            }

            \DB::commit();

            return response([
                'code' => 0,
                'msg'  => '更新成功'
            ]);
        } catch (\Exception $exception) {
            \DB::rollBack();
            return response(['msg' => $exception->getMessage()], 400);
        }
    }

    public function changeStatus(Request $request)
    {
        $this->article->whereId($request->id)->update(['status' => $request->status]);

        return response([
            'code' => 0,
            'msg'  => '更新成功'
        ]);
    }

    public function del(Article $article)
    {
        $this->authorize('destroy', $article);
        $article->delete();

        return response([
            'code' => 0,
            'msg'  => '删除成功'
        ]);
    }

    // 上传图片到又拍云
    public function upload(Request $request)
    {
        return Storage::disk('upyun')->put('/', $request->file('image'));
    }

    // 图片路径保存
    public function add_images($paths, $article_id)
    {
        $data = [];
        foreach ($paths as $k => $val) {
            $data[$k] = [
                'article_id' => $article_id,
                'image_name' => $val,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ];
        }

        return Images::insert($data);
    }

    // 删除又拍云上的图片
    public function del_images($paths)
    {
        $drive = Storage::disk('upyun');

        foreach ($paths as $k => $path) {
            $drive->delete($path);
        }

    }
}
