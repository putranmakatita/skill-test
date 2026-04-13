<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    // 4-1. Index: Paginated, eager-load user, active only
    public function index()
    {
        $posts = Post::with('user')
            ->active()
            ->latest()
            ->paginate(20);

        return response()->json($posts);
    }

    // 4-4. Show: 404 if not active
    public function show($id)
    {
        // active() scope + findOrFail satisfies the 404 requirement
        $post = Post::active()->with('user')->findOrFail($id);

        return response()->json($post);
    }

    // 4-2. Create
    public function create()
    {
        return 'posts.create';
    }

    // 4-3. Store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_draft' => 'required|boolean',
            'published_at' => 'nullable|date',
        ]);

        $post = Auth::user()->posts()->create($validated);

        return response()->json($post, 201);
    }

    // 4-5. Edit
    public function edit(Post $post)
    {
        Gate::authorize('update', $post);
        return 'posts.edit';
    }

    // 4-6. Update
    public function update(Request $request, Post $post)
    {
        Gate::authorize('update', $post);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'is_draft' => 'sometimes|boolean',
            'published_at' => 'nullable|date',
        ]);

        $post->update($validated);

        return response()->json($post);
    }

    // 4-7. Destroy
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);
        $post->delete();

        return response()->json(null, 204);
    }
}
