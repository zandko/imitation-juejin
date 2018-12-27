<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ArticleRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Lable;
use App\Models\User;
use App\Services\ArticleSearchBuilder;
use App\Transformers\ArticleTransformer;
use Auth;
use Illuminate\Http\Request;
use App\Models\Image;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class ArticlesController extends Controller
{
    /**
     * 发表文章
     * @param ArticleRequest $request
     * @param Article $article
     * @return \Dingo\Api\Http\Response
     */
    public function store(ArticleRequest $request, Article $article)
    {
        $user = Auth::guard('api')->user();

        $article->fill($request->all());
        $article->user_id = $user->id;
        $article->user_name = $user->name;

        if ($request->article_image_id) {
            $image = Image::find($request->article_image_id);
            $article->image = $image->path;
        }

        /*文章状态  后续开发 */
        $article->state = 1;

        $article->save();

        return $this->response->item($article, new ArticleTransformer())->setStatusCode(201);
    }

    /**
     * 修改文章
     * @param ArticleRequest $request
     * @param Article $article
     * @return \Dingo\Api\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(ArticleRequest $request, Article $article)
    {
        $this->authorize('own', $article);

        if ($request->article_image_id) {
            $image = Image::find($request->article_image_id);
            $request['article_image_id'] = $image->path;
        }
        $article->update($request->all());
        return $this->response->item($article, new  ArticleTransformer());
    }

    /**
     * 删除文章
     * @param Article $article
     * @return \Dingo\Api\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Article $article)
    {
        $this->authorize('own', $article);
        $article->delete();
        return $this->response->noContent();
    }

    /**
     * 文章详情
     * @param Article $article
     * @return \Dingo\Api\Http\Response
     */
    public function show(Article $article)
    {
        $article->increment('read_count', 1);
        return $this->response->item($article, new ArticleTransformer());
    }

    /**
     * 文章列表
     * @param Request $request
     * @param Article $article
     * @return \Dingo\Api\Http\Response
     */
    public function index(Request $request, Article $article)
    {
        $page = $request->input('page',1);
        $perPage = 20;

        $builder = (new ArticleSearchBuilder())->state()->paginate($perPage,$page);

        if($search = $request->input('search')) {
            $keywords = array_filter(explode(' ', $search));
            $builder->keywords($keywords);
        }


        if ($user = Auth::guard('api')->user()) {
            $lable = $user->userLable;

            $lableId = [];
            foreach ($lable as $v) {
                $lableId[] = $v['id'];
            }

            $builder->isLoginTag($lableId);

        }else {
            $builder->isLoginTag();
        }


        if ($request->input('category_id') && $category = Category::find($request->input('category_id'))) {
            $builder->category($category);
        }

        if ($request->input('lable_id') && $lable = Lable::find($request->input('lable_id'))) {
            $builder->lable($lable);
        }

        switch ($request->order) {
            case 'recent' :
                $builder->orderBy('id','desc');
                break;
            case 'recentReplied' :
                $builder->orderBy('reply_count','desc');
                break;
            case 'weeklyHottest' :
                $builder->DateTimeOrderBy(Carbon::now()->startOfWeek()->toDateTimeString(),Carbon::now()->endOfWeek()->toDateTimeString(),false);
                break;
            case 'monthlyHottest' :
                $builder->DateTimeOrderBy(Carbon::now()->startOfMonth()->toDateTimeString(),Carbon::now()->endOfMonth()->toDateTimeString(),false);
                break;
            case 'hottest' :
                $builder->DateTimeOrderBy();
                break;
            default:
                $builder->popular();
                break;
        }

        $result = app('es')->search($builder->getParams());

        $articleIds = collect($result['hits']['hits'])->pluck('_id')->all();

        $articles = Article::query()
            ->whereIn('id',$articleIds)
            ->orderByRaw(DB::raw("FIND_IN_SET(id,'" . join(',', $articleIds) . "'" . ')'))
            ->get();

        $article = new LengthAwarePaginator($articles, $result['hits']['total'], $perPage, $page, [
            'path' => app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('api.articles.index')
        ]);

        return $this->response->paginator($article, new ArticleTransformer());
    }

    /**
     * 个人文章列表
     * @param User $user
     * @return \Dingo\Api\Http\Response
     */
    public function userIndex(User $user)
    {
        $article = $user->article()->recentLiked()->paginate(20);

        return $this->response->item($article, new ArticleTransformer());
    }

    /**
     * 点赞 取消点赞
     * @param Article $article
     * @return \Dingo\Api\Http\Response
     * @throws \Exception
     */
    public function like(Article $article)
    {
        $like = Auth::guard('api')->user()->likeThisArticle($article->id);

        if (count($like['attached']) > 0) {
            $article->increment('like_count', 1);

            return $this->response->created();
        } else {

            if ($article->like_count > 1) {
                $article->decrement('like_count', 1);
            }

            return $this->response->noContent();
        }

    }

    /**
     * 收藏文章、取消文章
     * @param Article $article
     * @return \Dingo\Api\Http\Response
     */
    public function collection(Article $article)
    {
        $collection = Auth::guard('api')->user()->userThisCollection($article->id);

        if (count($collection['attached']) > 0) {
            return $this->response->created();
        } else {
            return $this->response->noContent();
        }

    }

}
