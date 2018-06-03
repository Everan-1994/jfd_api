<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class AuthorizationsController extends Controller
{
    public function login(Request $request)
    {

        $username = $request->username;

        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['phone'] = $username;

        $credentials['password'] = $request->password;

        if (!$token = \Auth::guard('api')->attempt($credentials)) {
            return response(['error' => '账号或密码错误'], 400);
        }

        $user = \Auth::guard('api')->user();

        if ($user['status'] !== 1) {
            $this->logout(); // 退出登陆态
            return response(['error' => '账号已被冻结，请联系管理员。'], 400);
        }
        // 记录登入日志
        // event(new LoginEvent(\Auth::guard('api')->user(), new Agent(), $request->getClientIp()));


        // 使用 Auth 登录用户
        return (new UserResource($user))->additional(['meta' => [
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => \Auth::guard('api')->factory()->getTTL() * 60
        ]]);
    }

    public function logout()
    {
        \Auth::guard('api')->logout();

        return response()->json([
            'code' => 0,
            'msg'  => '退出成功'
        ]);
    }

    public function checkStatus($map)
    {
        $map['status'] = 1;

        return \Auth::attempt($map);
    }
}
