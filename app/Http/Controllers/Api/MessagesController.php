<?php

namespace App\Http\Controllers\Api;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Resources\MessageResource;

class MessagesController extends Controller
{
    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function index(Request $request)
    {
        $user = \Auth::user();
        $id = $user['id'];
        $identify = $user['identify'];

        $message = $this->message->when(($identify == 3 || isset($request->share_id)), function ($query) use ($id) {
            return $query->whereShareId($id);
        })
            ->when(isset($request->status), function ($query) use ($request) {
                return $query->whereStatus($request->status);
            })
            ->orderBy($request->order ?: 'created_at', $request->sort ?: 'desc')
            ->paginate($request->pageSize, ['*'], 'page', $request->page ?: 1);

        return MessageResource::collection($message);
    }

    public function store(Request $request)
    {
        $ip = $request->getClientIp();

        $exists = $this->message->where([
            'ip'         => $ip,
            'article_id' => $request->article_id,
            'status'     => 0
        ])->exists();

        if ($exists) {
            return response([
                'msg' => '该项目已存在留言'
            ], 400);
        }

        $this->message->create([
            'name'       => $request->name,
            'phone'      => $request->phone,
            'share_id'   => $request->share_id,
            'article_id' => $request->article_id,
            'ip'         => $ip,
            'home_type'  => implode(',', $request->type)
        ]);

        return response([
            'msg' => '留言成功'
        ], 201);
    }

    public function changeStatus(Request $request)
    {
        $this->message->whereId($request->id)->update([
            'status'     => $request->status,
            'updated_at' => now()->toDateTimeString()
        ]);

        return response([
            'code' => 0,
            'msg'  => '更新成功'
        ]);
    }

    public function remake(Request $request)
    {
        $data = [
            'remake' => $request->remake
        ];
        if ($request->status) {
            $data['status'] = $request->status;
            $data['updated_at'] = now()->toDateTimeString();
        }
        $this->message->whereId($request->id)->update($data);

        return response([
            'code' => 0,
            'msg'  => '备注成功'
        ]);
    }
}
