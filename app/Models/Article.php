<?php

namespace App\Models;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'title', 'content', 'category_id', 'lable_id', 'image',
        'state', 'order'
    ];

    /**
     * 用户
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 分类
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 标签
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lable()
    {
        return $this->belongsTo(Lable::class);
    }

    /**
     * 回复
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reply()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * 时间排序
     * @param $query
     * @return mixed
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }

    /**
     * 回复次数排序
     * @param $query
     * @return mixed
     */
    public function scopeRecentReplied($query)
    {
        return $query->orderBy('reply_count', 'desc');
    }

    /**
     * 热门排序
     * @param $query
     * @return mixed
     */
    public function scopePopular($query)
    {
        return $query->orderBy('read_count', 'desc');
    }

    /**
     * 本周最热
     * @param $query
     * @return mixed
     */
    public function scopeWeeklyHottest($query)
    {
        return $query->where('updated_at', '>=', Carbon::now()->startOfWeek())
            ->where('updated_at', '<=', Carbon::now()->endOfWeek())
            ->orderBy('like_count', 'desc');
    }

    /**
     * 本月最热
     * @param $query
     * @return mixed
     */
    public function scopeMonthlyHottest($query)
    {
        return $query->where('updated_at', '>=', Carbon::now()->startOfMonth())
            ->where('updated_at', '<=', Carbon::now()->endOfMonth())
            ->orderBy('like_count', 'desc');
    }

    /**
     * 历史最热
     * @param $query
     * @return mixed
     */
    public function scopeHottest($query)
    {
        return $query->orderBy('like_count', 'desc');
    }

    /**
     * 存入Elasticsearch
     * @return array
     */
    public function toESArray()
    {
        $arr = array_only($this->toArray(), [
            'id', 'title', 'user_id', 'user_name', 'category_id', 'lable_id', 'state', 'reply_count'
        ]);

        $arr['content'] = strip_tags($this->content);
        $arr['category'] = $this->category->name;
        $arr['category_description'] = strip_tags($this->category->description);
        $arr['lable'] = $this->lable ? $this->lable->name : '';
        $arr['lable_description'] = $this->lable ? strip_tags($this->lable->description) : '';
        $arr['created_at'] = strtotime($this->created_at);
        $arr['popular_order'] = $this->read_count + ($this->like_count * 3) + ($this->reply_count * 5);

        return $arr;
    }
}
