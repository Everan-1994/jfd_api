<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class UsersController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index(Request $request)
    {
        $user = $this->user->whereIdentify($request->identify)
            ->when(isset($request->status), function ($query) use ($request) {
                return $query->whereStatus($request->status);
            })
            ->when(isset($request->name), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })
            ->when(isset($request->sex), function ($query) use ($request) {
                return $query->whereSex($request->sex);
            })
            ->orderBy($request->order ?: 'created_at', $request->sort ?: 'desc')
            ->paginate($request->pageSize, ['*'], 'page', $request->page ?: 1);

        return UserResource::collection($user);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function store(UserRequest $userRequest)
    {
        $password = $userRequest->password ?: 'jfd168';

        $user = $this->user->create([
            'name'     => $userRequest->name,
            'phone'    => $userRequest->phone,
            'remake'   => $userRequest->remake,
            'password' => bcrypt($password),
            'identify' => $userRequest->identify ?: 3, // 默认员工
            'sex'      => $userRequest->sex,
            'status'   => $userRequest->status
        ]);

        return new UserResource($user);
    }

    public function update(UserRequest $userRequest)
    {
        $data = [
            'name'       => $userRequest->name,
            'phone'      => $userRequest->phone,
            'remake'     => $userRequest->remake,
            'sex'        => $userRequest->sex,
            'status'     => $userRequest->status,
            'updated_at' => now()->toDateTimeString()
        ];

        if (!empty($userRequest->password)) {
            $data['password'] = bcrypt($userRequest->password);
        }

        $this->user->whereId($userRequest->id)->update($data);

        return response([
            'code' => 0,
            'msg'  => '更新成功'
        ]);
    }

    public function changeStatus(Request $request)
    {
        $this->user->whereId($request->user_id)->update(['status' => $request->status]);

        return response([
            'code' => 0,
            'msg'  => '更新成功'
        ]);
    }

    public function del(User $user)
    {
        $user->delete();

        return response([
            'code' => 0,
            'msg'  => '删除成功'
        ]);
    }

    public function total()
    {
        $user = \Auth::user();
        $id = $user->id;
        $identify = $user->identify;

        if ($identify < 3) {
            $data['users'] = $this->user->count();
        }

        // 文章总数 && 留言总数 && 阅读总数
        $articles = \DB::table('articles')->when($identify == 3, function ($query) use ($id) {
            return $query->whereAuthorId($id);
        })->select(\DB::raw('COUNT(id) as count'), \DB::raw('SUM(true_views) as true_views'), \DB::raw('SUM(true_asks) as true_asks'))
            ->get()->toArray();
        $data['articles'] = $articles[0]->count;
        $data['true_views'] = $articles[0]->true_views ?: 0;
        $data['true_asks'] = $articles[0]->true_asks ?: 0;

        return response()->json([
            'code'  => 0,
            'data' => $data
        ]);
    }

    // 修改密码
    public function changePwd(Request $request, User $user)
    {
        $this->authorize('changePwd', $user);
        $user->password = bcrypt($request->new_pwd);
        $user->save();

        return response([
            'code' => 0,
            'msg' => 'Success'
        ]);
    }
}
