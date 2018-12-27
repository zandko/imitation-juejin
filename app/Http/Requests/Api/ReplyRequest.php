<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class ReplyRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'required | string | max:255',
//            'article_id' => 'required | exists:articles,id',
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'content' => '回复内容',
//            'article_id' => '文章',
        ];
    }
}
