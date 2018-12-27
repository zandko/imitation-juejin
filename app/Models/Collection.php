<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'article_id'];
}
