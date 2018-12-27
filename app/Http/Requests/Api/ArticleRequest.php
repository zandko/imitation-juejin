<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class ArticleRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'title' => 'required | string',
                    'content' => 'required | string',
                    'category_id' => 'required | exists:categories,id',
                    'lable_id' => 'nullable | exists:lables,id',
                ];
                break;

            case 'PATCH':
                return [
                    'title' => ' string',
                    'content' => ' string',
                    'category_id' => 'exists:categories,id',
                    'lable_id' => 'exists:lables,id',
                ];
                break;
        }

    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'title' => '标题',
            'content' => '内容',
            'category_id' => '分类',
            'lable_id' => '标签',
        ];
    }

}
