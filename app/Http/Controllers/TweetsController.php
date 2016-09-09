<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tweet;
use App\TwitterService;

class TweetsController extends Controller
{
    protected $twitter;
    
    function __construct(TwitterService $twitter)
    {
        $this->twitter = $twitter;
    }

    public function index()
    {
        return Tweet::limit(15)->get();
    }

    public function search(Request $request)
    {
        return $this->twitter->search($request->input('q'), 10);
    }
}
