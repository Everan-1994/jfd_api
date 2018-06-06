<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title'       => 'required',
            'description' => 'required',
            'keywords'    => 'required',
            'subtitle'    => 'required',
            'tags'        => 'required',
            'up_body'     => 'required',
            'down_body'   => 'required',
            'views'       => 'required',
            'asks'        => 'required'
        ];
    }

    public function messages()
    {
        return [
            'title.required'       => '标题不能为空',
            'description.required' => '简介不能为空',
            'keywords.required'    => '关键字不能为空',
            'subtitle.required'    => '副标题不能为空',
            'tags.required'        => '标签不能为空',
            'up_body.required'     => '上半区内容不能为空',
            'down_body.required'   => '下半区内容不能为空',
            'views.required'       => '阅读数不能为空',
            'asks.required'        => '咨询数不能为空'
        ];
    }
}
