<?php

namespace App\Http\Controllers;

use App\Models\Picture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Toggle the authenticated user's like on the given picture.
     */
    public function toggle(Request $request, Picture $picture): RedirectResponse|JsonResponse
    {
        $like = $picture->likes()->where('user_id', $request->user()->id)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $picture->likes()->create(['user_id' => $request->user()->id]);
            $liked = true;
        }

        $count = $picture->likes()->count();

        if ($request->wantsJson()) {
            return response()->json(['liked' => $liked, 'count' => $count]);
        }

        return back();
    }
}
