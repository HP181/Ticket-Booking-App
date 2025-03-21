<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $events = Event::where('is_published', true)
                      ->whereDate('event_date', '>=', now())
                      ->orderBy('event_date', 'asc')
                      ->paginate(9);
        
        return view('home', compact('events'));
    }
}
