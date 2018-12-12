<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Work;
use willvincent\Rateable\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function getWorkById($id) {
        return Work::with('ratings')->where('finalworkID', '=', $id)->firstOrFail();
    }
    
    // rating geven
    public function store(Request $request)
    {
        // doe dit weg later
        Auth::login(User::first()); // $request->user()->id;

        $userId = $request->user()->id;

        $work = $this->getWorkById($request->input('finalworkID'));

        // checken dat user_id not niet bestaat
        if ($work->ratings->contains(function(Rating $rating) use($userId) { 
            return (int)($rating->user_id) === (int)$userId;
        })) {
            return response()->json(['message' => 'You have already voted.']);
        }

        $rating = new Rating;
        $rating->rating = $request->input('rating');
        $rating->user_id = $userId;

        $work->ratings()->save($rating);

        return response()->json(['message' => 'Rating saved.']);
    }

    // rating opvragen
    public function show($id, Request $request)
    {
        $work = $this->getWorkById($id);

        return $work->ratings;
    }
}