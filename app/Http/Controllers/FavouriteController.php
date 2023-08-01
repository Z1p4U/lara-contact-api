<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Favourite;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $favourites = Favourite::all();

        return response()->json($favourites);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function favToggle(Request $request, string $id)
    {
        $user = auth()->user();
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Contact not found'], 404);
        }

        // Check if the user has already favorited the contact
        $isFavorited = $user->favorites()->where("contact_id", $id)->first();

        if ($isFavorited) {
            // If the Contact is already favorited, remove it from the favorite list
            $isFavorited->delete();
            return response()->json(['message' => 'Contact removed from favorites']);
        } else {
            // If the Contact is not favorited, add it to the favorite list
            $favorite = new Favourite(['contact_id' => $id]);
            $user->favorites()->save($favorite);
            return response()->json(['message' => 'Contact added to favorites']);
        }
    }
}
