<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    // 4-1. Index: Paginated, with User, Active only
    public function index(): JsonResponse
    {
        $posts = Post::with('user')->active()->latest()->paginate(20);
        return response()->json($posts);
    }

    // 4-2. Create
    public function create(): string
    {
        return 'posts.create';
    }

    // 4-3. Store
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_draft' => 'required|boolean',
            'published_at' => 'nullable|date',
        ]);

        $post = Auth::user()->posts()->create($data);
        return response()->json($post, 201);
    }

    // 4-4. Show: 404 if draft or scheduled
    public function show(string $id): JsonResponse
    {
        // active() scope + findOrFail satisfies 4-4 perfectly
        $post = Post::active()->with('user')->findOrFail($id);
        return response()->json($post);
    }

    // 4-5. Edit: Author only
    public function edit(Post $post): string
    {
        Gate::authorize('update', $post);
        return 'posts.edit';
    }

    // 4-6. Update: Author only
    public function update(Request $request, Post $post): JsonResponse
    {
        Gate::authorize('update', $post);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'is_draft' => 'sometimes|boolean',
            'published_at' => 'nullable|date',
        ]);

        $post->update($data);
        return response()->json($post);
    }

    // 4-7. Destroy: Author only
    public function destroy(Post $post): JsonResponse
    {
        Gate::authorize('delete', $post);
        $post->delete();
        return response()->json(null, 204);
    }
}
