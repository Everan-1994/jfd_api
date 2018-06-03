<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        switch ($this->method())
        {
            case 'POST':
                return [
                    'name'   => 'required',
                    'phone'  => [
                        'required',
                        'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/',
                        'unique:users'
                    ],
                    'remake' => 'required'
                ];
                break;
            case 'PATCH':
                return [
                    'name'   => 'required',
                    'phone'  => [
                        'required',
                        'regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/'
                    ],
                    'remake' => 'required'
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'name.required'   => '姓名不能为空',
            'phone.required'  => '手机号不能为空',
            'phone.regex'     => '手机格式不正确',
            'phone.unique'    => '手机号码已被使用',
            'remake.required' => '备注不能为空'
        ];
    }
}
