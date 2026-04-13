<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    // 4-1. Index: Paginated list of active posts
    public function index()
    {
        $posts = Post::with('user')
            ->published()
            ->paginate(20);

        return response()->json($posts);
    }

    // 4-2. Create: Authenticated only
    public function create()
    {
        return 'posts.create';
    }

    // 4-3. Store: Validate and Create
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
            'published_at' => 'nullable|date',
        ]);

        $post = Auth::user()->posts()->create($validated);

        return response()->json($post, 201);
    }

    // 4-4. Show: Single active post (404 if draft/scheduled)
    public function show($id)
    {
        $post = Post::published()->with('user')->findOrFail($id);

        return response()->json($post);
    }

    // 4-5. Edit: Author only
    public function edit(Post $post)
    {
        Gate::authorize('update', $post);
        return 'posts.edit';
    }

    // 4-6. Update: Author only + Validation
    public function update(Request $request, Post $post)
    {
        Gate::authorize('update', $post);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'body'  => 'sometimes|required|string',
            'published_at' => 'nullable|date',
        ]);

        $post->update($validated);

        return response()->json($post);
    }

    // 4-7. Destroy: Author only
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
