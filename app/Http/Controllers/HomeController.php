<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\NewsPost;

class HomeController extends Controller
{
    public function index()
    {
        $featuredPost = NewsPost::published()->featured()->latestFirst()->first();

        $secondaryPosts = NewsPost::published()
            ->where('is_featured', false)
            ->latestFirst()
            ->take(2)
            ->get();

        $upcomingEvents = Event::upcoming()->take(3)->get();

        return view('home', compact('featuredPost', 'secondaryPosts', 'upcomingEvents'));
    }

    public function subscribe()
    {
        return redirect()->route('home')->with('success', 'Thanks for subscribing to Starlight Academy updates.');
    }
}