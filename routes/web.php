<?php

Route::get('/wap', function () {
    return view('login');
})->name('login');
// 文章列表
Route::get('/index', function () {
    return view('index');
})->name('index');
// 留言列表
Route::get('/message', function () {
    return view('message');
})->name('message');
