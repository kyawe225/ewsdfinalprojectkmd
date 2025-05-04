<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of comments with pagination.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10); // Default: 10 comments per page

        // Check if `blog_id` is provided
        if (!$request->has('blog_id')) {
            return response()->json(['error' => 'Blog ID is required.'], 400);
        }

        $comments = Comments::where('blog_id', $request->blog_id)
            ->with(['tutor', 'student'])
            ->paginate($perPage);

        return response()->json($comments, 200);
    }


    /**
     * Store a newly created comment.
     */
    public function store(Request $request)
    {
        Log::info("Incoming request with token", ['headers' => $request->header('Authorization')]);

        // Detect the authenticated user
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized. Please log in as a tutor or student.'], 401);
        }

        // Validate request
        $request->validate([
            'content' => 'required|string|max:345',
            'blog_id' => 'required|exists:blogs,id',
        ]);

        // Allow users to comment multiple times
        $commentData = [
            'content' => $request->content,
            'blog_id' => $request->blog_id,
            'student_id' => $user instanceof \App\Models\Student ? $user->id : null,
            'tutor_id' => $user instanceof \App\Models\Tutor ? $user->id : null,
        ];

        $comment = Comments::create($commentData);

        return response()->json([
            'message' => 'Comment added successfully!',
            'comment' => $comment
        ], 201);
    }



    /**
     * Display a specific comment.
     */
    public function show($id)
    {
        $comment = Comments::with(['blog', 'student', 'tutor'])->find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        return response()->json($comment, 200);
    }

    /**
     * Update an existing comment.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'sometimes|string|max:345',
        ]);

        $comment = Comments::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        // Detect the logged-in user
        $user = Auth::guard('sanctum')->user();

        // Check if the logged-in user is the owner of the comment
        if (($user instanceof \App\Models\Tutor && $comment->tutor_id === $user->id) ||
            ($user instanceof \App\Models\Student && $comment->student_id === $user->id)
        ) {

            $comment->update($request->only('content'));

            return response()->json([
                'message' => 'Comment updated successfully!',
                'comment' => $comment
            ], 200);
        }

        return response()->json(['error' => 'Unauthorized. You can only update your own comments.'], 403);
    }


    /**
     * Remove a comment from the database.
     */
    public function destroy($id)
    {
        $comment = Comments::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        // Detect the logged-in user
        $user = Auth::guard('sanctum')->user();

        // Check if the logged-in user is the owner of the comment
        if (($user instanceof \App\Models\Tutor && $comment->tutor_id === $user->id) ||
            ($user instanceof \App\Models\Student && $comment->student_id === $user->id)
        ) {

            $comment->delete();

            return response()->json(['message' => 'Comment deleted successfully!'], 200);
        }

        return response()->json(['error' => 'Unauthorized. You can only delete your own comments.'], 403);
    }
}
