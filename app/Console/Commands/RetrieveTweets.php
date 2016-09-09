<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Movie;
use App\Tweet;
use App\TwitterService;
use Carbon\Carbon;

class RetrieveTweets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retrieve-tweets {movie : The title of the movie}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command retrieves all the possible tweets for the given movie title';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(TwitterService $twitterService)
    {
        $movieTitle = $this->argument('movie');

        $this->info('Command started:');
        $this->info("Retrieving tweets for the movie: {$movieTitle}");

        $movie = Movie::firstOrCreate(['name' => $movieTitle]);

        $remaining = 180;
        $bar = $this->output->createProgressBar($remaining);
        $bar->setFormat('very_verbose');

        while ($remaining > 0)
        {
            // Update max_id and since_id
            $maxIdTweet = Tweet::withMovie($movie)->orderBy('tweet_id', 'asc')->first();
            $sinceIdTweet = Tweet::withMovie($movie)->orderBy('tweet_id', 'desc')->first();

            $maxId = $maxIdTweet ? $maxIdTweet->tweet_id - 1 : null;
            // $sinceId = $sinceIdTweet ? $sinceIdTweet->tweet_id : null;

            // Get the tweets with a GET call with set parameters
            $response = $twitterService->search($movieTitle, 100, $maxId);

            if (!$response) {
                $remaining = 0;
                break;
            }

            $tweets = collect($response['tweets']);

            $bar->setMessage("Tweets retrieved: {$tweets->count()}");

            // Update remaining Twitter API calls
            $remaining = $response['xHeaders']['x_rate_limit_remaining'];
            if ($remaining >= 179) $bar->setProgress(180 - $remaining);
            $bar->setMessage("Remaining Twitter API calls: {$remaining}");

            // Map tweets to Tweet class format
            $tweets = $tweets->map(function($tweet) {
                return new Tweet([
                    'tweet_id' => $tweet->id,
                    'user' => $tweet->user->id,
                    'text' => $tweet->text,
                    'source' => $tweet->source,
                    'favorite_count' => $tweet->favorite_count,
                    'retweet_count' => $tweet->retweet_count,
                    'lang' => $tweet->lang,
                    'created_at' => Carbon::parse($tweet->created_at)->toDateTimeString(),
                ]);
            });

            // Associate tweets to movie and save them
            $movie->tweets()->saveMany($tweets);

            $bar->setMessage("{$tweets->count()} tweets saved to the db");
            $bar->setProgress(180 - $remaining);
        }

        $bar->finish();
        $this->info("Command finished!");
    }
}
