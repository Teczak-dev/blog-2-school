<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->get();
        return view('posts.index', [
            'posts' => $posts
        ]);
    }

    public function show(string $id)
    {
        $post = Post::with('comments')->findOrFail($id);

        return view('posts.show', [
            'post' => $post,
        ]);
    }

    public function create()
    {
        return view('posts.create');
    }

    public function edit(string $id)
    {
        $post = Post::findOrFail($id);
        
        // Check if user can edit this post
        if (auth()->user() && $post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('posts.edit', [
            'post' => $post
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|min:5|max:255',
            'category' => 'nullable|min:2|max:255',
            'category_color' => 'nullable|string|in:blue,green,purple,red,yellow,indigo,pink,gray',
            'lead' => 'nullable|max:500',
            'content' => 'required|min:10',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string',
            'read_time_minutes' => 'nullable|integer|min:1|max:60',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('posts', 'public');
        }

        // Process tags
        $tags = null;
        if ($request->tags) {
            $tags = array_map('trim', explode(',', $request->tags));
            $tags = array_filter($tags); // Remove empty tags
        }

        // Auto-generate category if needed
        $category = $request->category ?: Post::generateCategory($request->title);
        
        // Auto-calculate read time if not provided
        $readTime = $request->read_time_minutes ?: Post::calculateReadTime($request->content);

        Post::create([
            'title' => $request->title,
            'category' => $category,
            'category_color' => $request->category_color ?: 'blue',
            'lead' => $request->lead,
            'content' => $request->content,
            'photo' => $photoPath,
            'author' => auth()->user()->name,
            'user_id' => auth()->id(),
            'is_published' => true,
            'tags' => $tags,
            'read_time_minutes' => $readTime,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post został utworzony!');
    }

    public function update(Request $request, string $id)
    {
        $post = Post::findOrFail($id);
        
        // Check if user can edit this post
        if (auth()->user() && $post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|min:5|max:255',
            'category' => 'nullable|min:2|max:255',
            'category_color' => 'nullable|string|in:blue,green,purple,red,yellow,indigo,pink,gray',
            'lead' => 'nullable|max:500',
            'content' => 'required|min:10',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string',
            'read_time_minutes' => 'nullable|integer|min:1|max:60',
        ]);

        $photoPath = $post->photo; // Keep existing photo by default
        
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($post->photo) {
                \Storage::disk('public')->delete($post->photo);
            }
            $photoPath = $request->file('photo')->store('posts', 'public');
        }

        // Process tags
        $tags = null;
        if ($request->tags) {
            $tags = array_map('trim', explode(',', $request->tags));
            $tags = array_filter($tags); // Remove empty tags
        }

        // Auto-calculate read time if not provided
        $readTime = $request->read_time_minutes ?: Post::calculateReadTime($request->content);

        $post->update([
            'title' => $request->title,
            'category' => $request->category ?: Post::generateCategory($request->title),
            'category_color' => $request->category_color ?: $post->category_color ?: 'blue',
            'lead' => $request->lead,
            'content' => $request->content,
            'photo' => $photoPath,
            'tags' => $tags,
            'read_time_minutes' => $readTime,
        ]);

        return redirect()->route('posts.show', $post->id)->with('success', 'Post został zaktualizowany!');
    }
}
