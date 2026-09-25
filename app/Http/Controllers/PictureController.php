<?php

namespace App\Http\Controllers;

use App\Gender;
use App\Http\Requests\StorePictureRequest;
use App\Models\Picture;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PictureController extends Controller
{
    /**
     * Display the main feed of the most recently uploaded pictures.
     */
    public function index(Request $request): View
    {
        $pictures = Picture::query()
            ->with(['user', 'likes', 'comments'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('user', function ($user) use ($request) {
                    $user->where('name', 'like', '%'.$request->string('search').'%');
                });
            })
            ->when($request->filled('gender'), function ($query) use ($request) {
                $query->whereHas('user', function ($user) use ($request) {
                    $user->where('gender', $request->string('gender'));
                });
            })
            ->when($request->filled('country'), function ($query) use ($request) {
                $query->whereHas('user', function ($user) use ($request) {
                    $user->where('country', $request->string('country'));
                });
            })
            ->when($request->filled('state'), function ($query) use ($request) {
                $query->whereHas('user', function ($user) use ($request) {
                    $user->where('state', $request->string('state'));
                });
            })
            ->when($request->filled('city'), function ($query) use ($request) {
                $query->whereHas('user', function ($user) use ($request) {
                    $user->where('city', $request->string('city'));
                });
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('pictures.index', [
            'pictures' => $pictures,
            'genders' => Gender::cases(),
        ]);
    }

    /**
     * Display a single picture with its comments.
     */
    public function show(Picture $picture): View
    {
        $picture->load(['user', 'comments.user', 'likes']);

        return view('pictures.show', [
            'picture' => $picture,
        ]);
    }

    /**
     * Store a newly uploaded picture.
     */
    public function store(StorePictureRequest $request): RedirectResponse
    {
        $path = $request->file('photo')->store('pictures', 'public');

        $picture = $request->user()->pictures()->create([
            'path' => $path,
            'caption' => $request->validated('caption'),
        ]);

        if ($request->boolean('set_as_profile_picture')) {
            $request->user()->update(['profile_picture_id' => $picture->id]);
        }

        return back()->with('status', 'picture-uploaded');
    }
}
