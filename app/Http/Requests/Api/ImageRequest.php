<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class ImageRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'type' => 'required|string|in:avatar,article',
        ];

        if ($this->type == 'avatar') {
            $rules['image'] = 'required|mimes:jpeg,bmp,png,gif|dimensions:min_width=200,min_height=200';
        } else {
            $rules['image'] = 'required|mimes:jpeg,bmp,png,gif';
        }

        return $rules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'image.dimensions' => '图片的清晰度不高,宽和高需要200px以上',
        ];
    }
}
