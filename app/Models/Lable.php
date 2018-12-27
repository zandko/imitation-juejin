<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lable extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'image', 'order'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function article()
    {
        return $this->hasMany(Article::class);
    }
}
