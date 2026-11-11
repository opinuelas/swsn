<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePictureRequest;
use App\Models\Picture;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PictureController extends Controller
{
    /**
     * Display the main feed of the most recently uploaded pictures.
     */
    public function index(): View
    {
        $pictures = Picture::query()
            ->with(['user', 'likes', 'comments'])
            ->latest()
            ->paginate(50);

        return view('pictures.index', [
            'pictures' => $pictures,
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

    /**
     * Delete one of the authenticated user's own pictures.
     */
    public function destroy(Request $request, Picture $picture): RedirectResponse
    {
        abort_unless($picture->user_id === $request->user()->id, 403);

        if ($request->user()->profile_picture_id === $picture->id) {
            $request->user()->update(['profile_picture_id' => null]);
        }

        Storage::disk('public')->delete($picture->path);

        $picture->delete();

        return back()->with('status', 'picture-deleted');
    }
}
