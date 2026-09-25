<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Picture;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    /**
     * Store a new comment on the given picture.
     */
    public function store(StoreCommentRequest $request, Picture $picture): RedirectResponse
    {
        $picture->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
        ]);

        return back()->with('status', 'comment-added');
    }
}
