<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sort = $request->get('sort', 'newest');
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $sinceId = $request->get('since_id');

        $query = Comment::with('replies')->whereNull('parent_id');

        if ($sinceId) {
            $query->where('id', '>', $sinceId);
            $query->orderBy('id', 'desc');
            $comments = $query->take(20)->get(); // Limit for real-time updates
            return response()->json([
                'success' => true,
                'comments' => $comments->reverse()->values(), // Reverse to chronological order
                'new_comments' => true
            ]);
        }

        switch ($sort) {
            case 'popular':
                $query->orderBy('likes', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $comments = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'comments' => $comments->items(),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
                'has_more' => $comments->hasMorePages(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'user_name' => $request->user_name,
            'content' => $request->content,
            'parent_id' => $request->parent_id,
            'likes' => 0,
        ]);

        if ($request->parent_id) {
            $comment->load('parent');
        }

        return response()->json([
            'success' => true,
            'message' => 'Comment posted successfully',
            'comment' => $comment
        ], 201);
    }

    public function like($id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
        $comment->increment('likes');

        return response()->json([
            'success' => true,
            'likes' => $comment->likes
        ]);
    }

    public function reply(Request $request, $id): JsonResponse
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
        ]);

        $parentComment = Comment::findOrFail($id);

        $reply = Comment::create([
            'user_name' => $request->user_name,
            'content' => $request->content,
            'parent_id' => $id,
            'likes' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reply posted successfully',
            'reply' => $reply
        ], 201);
    }
}
