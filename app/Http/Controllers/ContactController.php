<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactDetailResource;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Models\Favourite;
use App\Models\SearchHistory;
use App\Models\Trash;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // SearchHistory::updateOrCreate(
        //     ['user_id' => Auth::id()],
        //     ['keyword' => request()->keyword]
        // );

        $contacts = Contact::when(request()->has("keyword"), function ($query) {
            $query->where(function (Builder $builder) {
                $user = auth()->user();
                $keyword = request()->keyword;

                $builder->where("name", "like", "%" . $keyword . "%");
                $builder->orWhere("phone_number", "like", "%" . $keyword . "%");
                $user->searchHistories()->updateOrCreate(['keyword' => $keyword]);
            });
        })
            ->latest("id")
            ->paginate(5)
            ->withQueryString();

        return ContactResource::collection($contacts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "country_code" => "required|min:1|max:265",
            "phone_number" => "required",
        ]);

        $contact = Contact::create([
            "user_id" => Auth::id(),
            "name" => $request->name,
            "country_code" => $request->country_code,
            "phone_number" => $request->phone_number,
        ]);

        return $request;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $contact = Contact::find($id);
        if (is_null($contact)) {
            return response()->json([
                "message" => "Contact not found",
            ], 404);
        }
        // return response()->json([
        //     "data" => $contact
        // ]);
        return new ContactDetailResource($contact);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "name" => "nullable|min:3|max:20",
            "country_code" => "nullable|integer|min:1|max:265",
            'phone_number' => "nullable|min:7|max:15"
        ]);

        $contact = Contact::find($id);
        if (is_null($contact)) {
            return response()->json([
                "message" => "Contact not found",
            ], 404);
        }

        // $contact->update([
        //     "name" => $request->name,
        //     "country_code" => $request->country_code,
        //     "phone_number" => $request->phone_number
        // ]);

        // $contact->update($request->all());

        if ($request->has('name')) {
            $contact->name = $request->name;
        }

        if ($request->has('country_code')) {
            $contact->country_code = $request->country_code;
        }

        if ($request->has('phone_number')) {
            $contact->phone_number = $request->phone_number;
        }

        $contact->update();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contact = Contact::find($id);
        if (is_null($contact)) {
            return response()->json([
                "message" => "Contact not found",
            ], 404);
        }

        $contact->delete();

        return response()->json([
            "message" => "Contact is deleted."
        ]);
    }

    public function favList()
    {
        $favourites = Favourite::all();

        return response()->json($favourites);
    }

    public function toggleFav(Request $request, string $id)
    {
        $user = auth()->user();
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Contact not found'], 404);
        }

        // Check if the user has already favorited the contact
        $isFavorited = $user->favorites->contains($contact);

        if ($isFavorited) {
            // If the Contact is already favorited, remove it from the favorite list
            $user->favorites()->detach($contact);
            return response()->json(['message' => 'Contact removed from favorites']);
        } else {
            // If the Contact is not favorited, add it to the favorite list
            $user->favorites()->attach($contact);
            return response()->json(['message' => 'Contact added to favorites']);
        }
    }

    public function trash()
    {
        $softDeletedArticles = Contact::onlyTrashed()->get();

        return response()->json($softDeletedArticles);
    }

    public function restore(string $id)
    {
        $contact = Contact::withTrashed()->find($id);

        $contact->restore();

        return response()->json(['message' => 'Contact is restored from trash']);
    }

    public function forceDelete(string $id)
    {

        $contact = Contact::withTrashed()->findOrFail($id);

        $contact->forceDelete();
        return response()->json(['message' => 'Contact is deleted permanently']);
    }
}
