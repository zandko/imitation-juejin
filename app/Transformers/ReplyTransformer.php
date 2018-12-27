<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Reply;

class ReplyTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user'];

    /**
     * @param Reply $reply
     * @return array
     */
    public function transform(Reply $reply)
    {
        return [
            'user_id' => $reply->user_id,
            'article_id' => $reply->article_id,
            'content' => $reply->content,
            'created_at' => $reply->created_at->diffForHumans(),
            'updated_at' => $reply->updated_at->diffForHumans(),
        ];
    }

    /**
     * @param Reply $reply
     * @return \League\Fractal\Resource\Item
     */
    public function includeUser(Reply $reply)
    {
        return $this->item($reply->user,new UserTransformer());
    }
}