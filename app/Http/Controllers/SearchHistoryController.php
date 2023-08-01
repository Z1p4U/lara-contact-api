<?php

namespace App\Http\Controllers;

use App\Models\SearchHistory;
use Illuminate\Http\Request;

class SearchHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retrieve all search history records
        $searchHistory = SearchHistory::all();

        return response()->json($searchHistory);
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
        $user = auth()->user();
        $searchHistory = SearchHistory::findOrFail($id);

        if ($searchHistory->user_id === $user->id) {
            $searchHistory->delete();
            return response()->json(['message' => 'Search history deleted successfully']);
        } else {
            return response()->json(['message' => 'You are not authorized to delete this search history'], 403);
        }
    }
}
