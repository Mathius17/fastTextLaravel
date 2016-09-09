<?php

namespace App;

use Movie;
use Tweet;
use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterService
{
    protected $connection;
    
    function __construct()
    {
        $consumer_key = env('TWITTER_CONSUMER_KEY');
        $consumer_secret = env('TWITTER_CONSUMER_SECRET');
        $access_token = env('TWITTER_ACCESS_TOKEN');
        $access_token_secret = env('TWITTER_ACCESS_TOKEN_SECRET');

        $this->connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
    }

    public function search($query, $count = 15, $maxId = null, $sinceId = null)
    {
        $response = $this->connection->get('search/tweets', [
            'q' => $query,
            'count' => $count,
            'max_id' => $maxId,
            'since_id' => $sinceId,
            'lang' => 'en',
            'result_type' => 'recent',
            'include_entities' => false,
        ]);
        
        if ($this->connection->getLastHttpCode() != 200) return null;

        return [
            'httpCode' => $this->connection->getLastHttpCode(),
            'tweets' => $response->statuses,
            'xHeaders' => $this->connection->getLastXHeaders(),
        ];
    }
}
