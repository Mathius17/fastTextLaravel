<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Get the tweets of the movie.
     */
    public function tweets()
    {
        return $this->hasMany(Tweet::class);
    }
}
