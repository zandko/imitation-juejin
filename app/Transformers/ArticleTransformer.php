<?php

namespace App\Transformers;

use App\Models\Article;
use League\Fractal\TransformerAbstract;

class ArticleTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user','category','lable'];

    /**
     * @param Article $article
     * @return array
     */
    public function transform(Article $article)
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'content' => $article->content,
            'user_id' => $article->user_id,
            'user_name' => $article->user_name,
            'category_id' => $article->category_id,
            'lable_id' => $article->lable_id,
            'image' => $article->image,
            'state' => $article->state,
            'order' => $article->order ?: 0,
            'read_count' => $article->read_count ?: 0,
            'like_count' => $article->like_count ?: 0,
            'reply_count' => $article->reply_count ?: 0,
            'created_at' => $article->created_at->diffForHumans(),
            'updated_at' => $article->updated_at->diffForHumans(),
        ];
    }

    /**
     * @param Article $article
     * @return array
     */
    public function includeUser(Article $article)
    {
        return $this->item($article->user, new UserTransformer());
    }

    /**
     * @param Article $article
     * @return \League\Fractal\Resource\Item
     */
    public function includeCategory(Article $article)
    {
        return $this->item($article->category, new CategoryTransformer());
    }

    /**
     * @param Article $article
     * @return \League\Fractal\Resource\Item
     */
    public function includeLable(Article $article)
    {
        return $this->item($article->lable, new LableTransformer());
    }

    /**
     * @param Article $article
     * @return \League\Fractal\Resource\Item
     */
//    public function includeReply(Article $article)
//    {
//        return $this->collection($article->reply, new ReplyTransformer());
//    }
}