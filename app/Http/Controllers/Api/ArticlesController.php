<?php

namespace App\Http\Controllers\Api;

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
        $article = $this->article->whereAuthorId(\Auth::id())
            ->when(isset($request->status), function ($query) use ($request) {
                return $query->whereStatus($request->status);
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
        $this->article->whereId($request->id)->increment('views', 1, ['true_views'=> \DB::raw('true_views  + 1')]);

        return response([
            'code' => 0,
            'msg'  => 'Success'
        ]);
    }

    public function store(ArticleRequest $articleRequest, Tag $tags)
    {
        \DB::beginTransaction();
        try {
            $article = $this->article->create([
                'title'     => $articleRequest->title,
                'subtitle'  => $articleRequest->subtitle,
                'up_body'   => $articleRequest->up_body,
                'down_body' => $articleRequest->down_body,
                'views'     => $articleRequest->views,
                'asks'      => $articleRequest->asks,
                'author_id' => \Auth::id(),
                'status'    => $articleRequest->status
            ]);

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

            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollBack();
            return response(['msg' => $exception->getMessage()], 400);
        }

        return new ArticleResource($article);
    }

    public function update(ArticleRequest $articleRequest, Tag $tags)
    {
        \DB::beginTransaction();
        try {
            $this->article->whereId($articleRequest->id)
                ->update([
                    'title'     => $articleRequest->title,
                    'subtitle'  => $articleRequest->subtitle,
                    'up_body'   => $articleRequest->up_body,
                    'down_body' => $articleRequest->down_body,
                    'views'     => $articleRequest->views,
                    'asks'      => $articleRequest->asks,
                    'status'    => $articleRequest->status
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

    public function upload(Request $request)
    {
        $result = Storage::disk('upyun')->put('/', $request->file('file'));

        return response()->json([
            'code' => 0,
            'image_url'  => $result
        ]);
    }
}
