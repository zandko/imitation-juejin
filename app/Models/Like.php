<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id','article_id'];

    /**
     * 没有时间字段
     * @var bool
     */
    public $timestamps = false;

}
