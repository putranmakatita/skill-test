<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    // 4-1. Index: Paginated, Published only, Eager-load user
    public function index()
    {
        $posts = Post::with('user')
            ->published()
            ->latest()
            ->paginate(20);

        // Best Practice: Use Resources for JSON consistency
        return PostResource::collection($posts);
    }

    // 4-2. Create: Simple string return
    public function create()
    {
        return 'posts.create';
    }

    // 4-3. Store: Use FormRequest for validation
    public function store(StorePostRequest $request)
    {
        $post = Auth::user()->posts()->create($request->validated());

        return new PostResource($post);
    }

    // 4-4. Show: 404 if not published
    public function show($id)
    {
        // Use findOrFail on the scope to trigger automatic 404
        $post = Post::published()->with('user')->findOrFail($id);

        return new PostResource($post);
    }

    // 4-5. Edit: Author only
    public function edit(Post $post)
    {
        Gate::authorize('update', $post);
        return 'posts.edit';
    }

    // 4-6. Update: Author only + Validation
    public function update(StorePostRequest $request, Post $post)
    {
        Gate::authorize('update', $post);

        $post->update($request->validated());

        return new PostResource($post);
    }

    // 4-7. Destroy: Author only
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return response()->json(['message' => 'Deleted'], 204);
    }
}
