<?php

namespace App\Transformers;

use App\Models\Lable;
use League\Fractal\TransformerAbstract;

class LableTransformer extends TransformerAbstract
{
    /**
     * @param Lable $lable
     * @return array
     */
    public function transform(Lable $lable)
    {
        return [
            'id' => $lable->id,
            'name' => $lable->name,
            'description' => $lable->description,
            'image' => $lable->image,
            'post_count' => $lable->post_count,
            'follow_count' => $lable->follow_count,
            'order' => $lable->order,
            'created_at' => $lable->created_at->toDateTimeString(),
            'updated_at' => $lable->updated_at->toDateTImeString(),
        ];
    }
}