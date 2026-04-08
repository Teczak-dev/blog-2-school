<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Events\PostCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with('user')->orderBy('created_at', 'desc')->orderBy('id', 'desc');
        
        // Following filter
        if ($request->get('filter') === 'following' && auth()->check()) {
            $followingUserIds = auth()->user()->following()->pluck('users.id');
            $query->whereIn('user_id', $followingUserIds);
        }
        
        // Global search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('lead', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }
        
        // Category filter
        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }
        
        // Author filter
        if ($author = $request->get('author')) {
            $query->where('author', $author);
        }
        
        // Date range filter
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        // Reading time filter
        if ($readTimeMin = $request->get('read_time_min')) {
            $query->where('read_time_minutes', '>=', $readTimeMin);
        }
        
        if ($readTimeMax = $request->get('read_time_max')) {
            $query->where('read_time_minutes', '<=', $readTimeMax);
        }
        
        // Tag filter
        if ($tag = $request->get('tag')) {
            $query->where('tags', 'like', "%{$tag}%");
        }
        
        $posts = $query->paginate(12)->withQueryString();
        
        // Get unique values for filter dropdowns
        $categories = Post::whereNotNull('category')->distinct()->pluck('category')->sort();
        $authors = Post::whereNotNull('author')->distinct()->pluck('author')->sort();
        $allTags = Post::whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->filter()
            ->flatten()
            ->unique()
            ->sort();
        
        return view('posts.index', [
            'posts' => $posts,
            'categories' => $categories,
            'authors' => $authors,
            'tags' => $allTags
        ]);
    }

    public function show(Request $request, string $id)
    {
        $post = Post::with(['comments', 'user'])->findOrFail($id);
        $relatedPosts = $post->getRelatedPosts();
        
        // Get sorting parameter
        $sort = $request->get('sort', 'newest');
        
        // Get top-level approved comments with sorting
        $commentsQuery = $post->approvedComments()->topLevel();
        
        switch ($sort) {
            case 'oldest':
                $commentsQuery->orderBy('created_at', 'asc');
                break;
            case 'most_liked':
                $commentsQuery->orderBy('likes_count', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'newest':
            default:
                $commentsQuery->orderBy('created_at', 'desc');
                break;
        }

        return view('posts.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'sort' => $sort,
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
            'category_color' => 'nullable|string|in:blue,green,purple,red,yellow,orange,indigo,pink,gray',
            'lead' => 'nullable|max:500',
            'content' => 'required|min:10',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Increased to 5MB, added webp
            'tags' => 'nullable|string',
            'read_time_minutes' => 'nullable|integer|min:1|max:60',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            try {
                $file = $request->file('photo');
                
                // Log upload attempt for debugging
                \Log::info('Photo upload attempt', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'temp_path' => $file->getPathname()
                ]);
                
                $photoPath = $file->store('posts', 'public');
                
                \Log::info('Photo uploaded successfully', [
                    'stored_path' => $photoPath,
                    'full_path' => storage_path('app/public/' . $photoPath)
                ]);
                
                // Verify file exists after upload
                if (!Storage::disk('public')->exists($photoPath)) {
                    \Log::error('Photo upload failed - file not found after storage', ['path' => $photoPath]);
                    return back()->withErrors(['photo' => 'Błąd uploadu zdjęcia - plik nie został zapisany']);
                }
                
            } catch (\Exception $e) {
                \Log::error('Photo upload failed with exception', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return back()->withErrors(['photo' => 'Błąd uploadu zdjęcia: ' . $e->getMessage()]);
            }
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

        $post = Post::create([
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

        // Dispatch event for followers notification
        PostCreated::dispatch($post);

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
            'category_color' => 'nullable|string|in:blue,green,purple,red,yellow,orange,indigo,pink,gray',
            'lead' => 'nullable|max:500',
            'content' => 'required|min:10',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Increased to 5MB, added webp
            'tags' => 'nullable|string',
            'read_time_minutes' => 'nullable|integer|min:1|max:60',
        ]);

        $photoPath = $post->photo; // Keep existing photo by default
        
        if ($request->hasFile('photo')) {
            try {
                $file = $request->file('photo');
                
                \Log::info('Photo update attempt', [
                    'post_id' => $post->id,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'old_photo' => $post->photo
                ]);
                
                // Delete old photo if exists
                if ($post->photo && Storage::disk('public')->exists($post->photo)) {
                    Storage::disk('public')->delete($post->photo);
                    \Log::info('Old photo deleted', ['path' => $post->photo]);
                }
                
                $photoPath = $file->store('posts', 'public');
                
                \Log::info('New photo uploaded', ['path' => $photoPath]);
                
                // Verify file exists after upload
                if (!Storage::disk('public')->exists($photoPath)) {
                    \Log::error('Photo update failed - file not found after storage', ['path' => $photoPath]);
                    return back()->withErrors(['photo' => 'Błąd uploadu zdjęcia - plik nie został zapisany']);
                }
                
            } catch (\Exception $e) {
                \Log::error('Photo update failed', [
                    'post_id' => $post->id,
                    'error' => $e->getMessage()
                ]);
                return back()->withErrors(['photo' => 'Błąd uploadu zdjęcia: ' . $e->getMessage()]);
            }
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

        return redirect()->route('posts.show', $post->id)
            ->with('success', 'Post został zaktualizowany!')
            ->with('cache_buster', time());
    }
}
