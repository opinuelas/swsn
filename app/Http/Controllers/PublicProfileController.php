<?php

namespace App\Http\Controllers;

use App\Gender;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicProfileController extends Controller
{
    /**
     * Search the profiles of other users.
     */
    public function index(Request $request): View
    {
        $profiles = User::query()
            ->with('profilePicture')
            ->whereKeyNot($request->user()->id)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->string('search').'%';

                $query->where(function ($query) use ($search) {
                    $query->where('userid', 'like', $search)->orWhere('name', 'like', $search);
                });
            })
            ->when($request->filled('gender'), fn ($query) => $query->where('gender', $request->string('gender')))
            ->when($request->filled('country'), fn ($query) => $query->where('country', $request->string('country')))
            ->when($request->filled('state'), fn ($query) => $query->where('state', $request->string('state')))
            ->when($request->filled('city'), fn ($query) => $query->where('city', $request->string('city')))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('profiles.index', [
            'profiles' => $profiles,
            'genders' => Gender::cases(),
        ]);
    }

    /**
     * Display a user's public profile and record the visit.
     */
    public function show(Request $request, User $user): View
    {
        $visitor = $request->user();

        if ($visitor->isNot($user)) {
            $user->profileVisits()->create(['visitor_id' => $visitor->id]);
            $user->increment('profile_views_count');
        }

        $user->load('profilePicture');

        return view('profiles.show', [
            'user' => $user,
            'pictures' => $user->pictures()->latest()->get(),
        ]);
    }

    /**
     * Display the log of who visited the authenticated user's public profile.
     */
    public function visitors(Request $request): View
    {
        return view('profiles.visitors', [
            'user' => $request->user(),
            'visits' => $request->user()->profileVisits()->with('visitor')->latest()->latest('id')->paginate(50),
        ]);
    }
}
