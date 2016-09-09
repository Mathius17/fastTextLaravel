<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tweet_id',
        'user',
        'text',
        'source',
        'favorite_count',
        'retweet_count',
        'lang',
        'created_at',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at'];

    /**
     * Get the movie of the tweet.
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    /**
     * Scope to retrieve the tweets that has the specified movie
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithMovie($query, Movie $movie)
    {
        return $query->whereHas('movie', function ($query) use ($movie) {
            $query->where('id', $movie->id);
        });
    }
}
