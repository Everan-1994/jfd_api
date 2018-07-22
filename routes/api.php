<?php

$api = app('Illuminate\Routing\Router');

// 后台API
$api->group([
    'namespace' => 'Api',
], function ($api) {

    $api->group([
        'middleware' => 'throttle: 20, 1', // 调用接口限制 1分钟10次
    ], function ($api) {
        // 登录
        $api->post('login', 'AuthorizationsController@login')
            ->name('api.authorizations.login');
        // 退出登陆
        $api->delete('logout', 'AuthorizationsController@logout')
            ->name('api.authorizations.index');
    });

    $api->group([
        'middleware' => 'throttle: 120, 1', // 调用接口限制 1分钟60次
    ], function ($api) {
        // 游客可以访问的api
        // 文章详情
        $api->get('article/{article}', 'ArticlesController@show')
            ->name('api.article.show');
        // 文章阅读数
        $api->patch('article/views', 'ArticlesController@views')
            ->name('api.article.views');
        // 留言
        $api->post('message', 'MessagesController@store')
            ->name('api.message.store');

        // 需要 token 验证的接口
        $api->group(['middleware' => 'refresh.token'], function ($api) {
            // 上传图片
            $api->post('upload/image', 'CommonsController@upload')
                ->name('api.common.upload');
            // 用户列表
            $api->get('user', 'UsersController@index')
                ->name('api.user.index');
            // 用户详情
            $api->get('user/{user}', 'UsersController@show')
                ->name('api.user.show');
            // 新增管理&员工
            $api->post('user', 'UsersController@store')
                ->name('api.user.store');
            // 变更状态
            $api->patch('user/status', 'UsersController@changeStatus')
                ->name('api.user.changeStatus');
            // 更新信息
            $api->patch('user', 'UsersController@update')
                ->name('api.user.update');
            // 删除用户
            $api->delete('user/{user}', 'UsersController@del')
                ->name('api.user.del');
            // 修改密码
            $api->patch('user/{user}/changePwd', 'UsersController@changePwd')
                ->name('api.user.changePwd');

            // 用户总数
            $api->get('total', 'UsersController@total')
                ->name('api.user.total');

            // 文章列表
            $api->get('article', 'ArticlesController@index')
                ->name('api.article.index');
            // 新增文章
            $api->post('article', 'ArticlesController@store')
                ->name('api.article.store');
            // 更新文章
            $api->patch('article', 'ArticlesController@update')
                ->name('api.article.update');
            // 变更状态
            $api->patch('article/status', 'ArticlesController@changeStatus')
                ->name('api.article.changeStatus');
            // 删除文章
            $api->delete('article/{article}', 'ArticlesController@del')
                ->name('api.article.del');
            // 文章图片
            $api->post('article/image', 'ArticlesController@upload')
                ->name('api.article.upload');

            // 报名列表
            $api->get('message', 'MessagesController@index')
                ->name('api.message.index');
            // 回访状态
            $api->patch('message/status', 'MessagesController@changeStatus')
                ->name('api.message.changeStatus');
            // 标记备注
            $api->patch('message/remake', 'MessagesController@remake')
                ->name('api.message.remake');
            // 删除留言
            $api->delete('message/{message}', 'MessagesController@del')
                ->name('api.message.del');

            // 最近七天内留言
            $api->get('week/messages', 'MessagesController@getWeekMessage')
                ->name('api.message.getWeekMessage');
            // 12个月内的留言
            $api->get('month/messages', 'MessagesController@getMonthMessage')
                ->name('api.message.getMonthMessage');
        });

    });
});