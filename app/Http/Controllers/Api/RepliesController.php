<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Reply;
use App\Transformers\ReplyTransformer;
use App\Http\Requests\Api\ReplyRequest;
use Auth;

class RepliesController extends Controller
{
    /**
     * 文章添加回复
     * @param ReplyRequest $request
     * @param Reply $reply
     * @return \Dingo\Api\Http\Response
     */
    public function store(ReplyRequest $request, Reply $reply, Article $article)
    {
        $reply->content = $request->content;
        $reply->article_id = $article->id;
        $reply->user_id = Auth::guard('api')->user()->id;
        $reply->save();

        return $this->response->item($reply, new ReplyTransformer())->setStatusCode(201);
    }

    /**
     * 删除回复
     * @param Reply $reply
     * @return \Dingo\Api\Http\Response
     * @throws \Exception
     */
    public function destroy(Article $article, Reply $reply)
    {
        if ($reply->article_id != $article->id) {
            return $this->response->errorBadRequest();
        }

        $this->authorize('own', $reply);
        $reply->delete();

        return $this->response->noContent();
    }

    public function index(Article $article)
    {
        $replies = $article->reply()->paginate(10);

        return $this->response->paginator($replies, new ReplyTransformer());
    }
}
