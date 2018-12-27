<?php

namespace App\Observers;

use App\Jobs\SyncOneArticleToES;
use App\Models\Article;

class ArticleObserver
{
    // creating, created, updating, updated, saving,
    // saved,  deleting, deleted, restoring, restored

    /**
     * 标签文章总数加1
     * @param Article $article
     */
    public function saved(Article $article)
    {
        $article->lable->increment('post_count', 1);

        // 放到Elasticsearch
        dispatch(new SyncOneArticleToES($article));
    }

    /**
     * @param Article $article
     */
    public function updated(Article $article)
    {
        // 放到Elasticsearch
        dispatch(new SyncOneArticleToES($article));
    }

    /**
     * 标签文章总数减1
     * @param Article $article
     */
    public function deleted(Article $article)
    {
        if ($article->post_count > 1) {
            $article->decrement('post_count', 1);
        }
    }
}
