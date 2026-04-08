<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;
    
    protected $fillable = ['title', 'category', 'category_color', 'lead', 'content', 'author', 'photo', 'is_published', 'user_id', 'tags', 'read_time_minutes'];

    protected $casts = [
        'tags' => 'array',
        'is_published' => 'boolean',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)
                    ->where(function($query) {
                        $query->where('is_approved', true)
                              ->orWhereNotNull('user_id'); // Logged users are auto-approved
                    });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Generate category from title if not provided
    public static function generateCategory($title)
    {
        // Extract main topic/category from title
        $words = explode(' ', $title);
        $category = $words[0]; // Use first word as category
        
        // Common categories mapping
        $categoryMappings = [
            'laravel' => 'Laravel',
            'php' => 'PHP', 
            'react' => 'React',
            'vue' => 'Vue.js',
            'javascript' => 'JavaScript',
            'docker' => 'Docker',
            'tutorial' => 'Tutorial',
            'guide' => 'Guide',
            'tips' => 'Tips',
            'how' => 'Tutorial',
            'what' => 'Guide',
            'introduction' => 'Guide'
        ];
        
        $lowerCategory = strtolower($category);
        return $categoryMappings[$lowerCategory] ?? ucfirst($lowerCategory);
    }

    // Auto-generate read time based on content
    public static function calculateReadTime($content)
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, ceil($wordCount / 200)); // Average 200 words per minute
    }

    // Get CSS classes for category color
    public function getCategoryColorClasses()
    {
        // Using inline styles for guaranteed color display
        $colorMap = [
            'blue' => 'bg-blue-600 text-white',           
            'green' => 'bg-green-600 text-white',         
            'purple' => 'bg-purple-600 text-white',       
            'red' => 'bg-red-600 text-white',             
            'yellow' => 'bg-yellow-500 text-black',       // yellow with black text
            'orange' => 'bg-orange-600 text-white',       
            'indigo' => 'bg-indigo-600 text-white',       
            'pink' => 'bg-pink-600 text-white',           
            'gray' => 'bg-gray-600 text-white',           
        ];

        return $colorMap[$this->category_color ?? 'blue'] ?? $colorMap['blue'];
    }
    
    // Get inline styles for category color (fallback method)
    public function getCategoryInlineStyles()
    {
        $colorMap = [
            'blue' => 'background-color: #2563eb; color: white;',           
            'green' => 'background-color: #16a34a; color: white;',         
            'purple' => 'background-color: #9333ea; color: white;',       
            'red' => 'background-color: #dc2626; color: white;',             
            'yellow' => 'background-color: #eab308; color: black;',       
            'orange' => 'background-color: #ea580c; color: white;',       
            'indigo' => 'background-color: #4f46e5; color: white;',       
            'pink' => 'background-color: #db2777; color: white;',           
            'gray' => 'background-color: #4b5563; color: white;',           
        ];

        return $colorMap[$this->category_color ?? 'blue'] ?? $colorMap['blue'];
    }

    // Get available category colors
    public static function getCategoryColors()
    {
        return [
            'blue' => 'Niebieski',
            'green' => 'Zielony', 
            'purple' => 'Fioletowy',
            'red' => 'Czerwony',
            'yellow' => 'Żółty',
            'orange' => 'Pomarańczowy',
            'indigo' => 'Indygo',
            'pink' => 'Różowy',
            'gray' => 'Szary',
        ];
    }

    // Get related posts based on category, tags and author
    public function getRelatedPosts($limit = 3)
    {
        $relatedPosts = collect();
        
        // Priority 1: Same category (excluding current post)
        $sameCategoryPosts = static::where('category', $this->category)
            ->where('id', '!=', $this->id)
            ->where('is_published', true)
            ->latest()
            ->take($limit)
            ->get();
            
        $relatedPosts = $relatedPosts->merge($sameCategoryPosts);
        
        // Priority 2: Similar tags (if we have tags and need more posts)
        if ($relatedPosts->count() < $limit && $this->tags) {
            $thisTags = $this->tags;
            $tagsPosts = static::where('id', '!=', $this->id)
                ->where('is_published', true)
                ->whereNotIn('id', $relatedPosts->pluck('id'))
                ->get()
                ->filter(function($post) use ($thisTags) {
                    if (!$post->tags) return false;
                    
                    // Check if any tags overlap
                    $intersection = array_intersect($thisTags, $post->tags);
                    return count($intersection) > 0;
                })
                ->sortByDesc('created_at')
                ->take($limit - $relatedPosts->count());
                
            $relatedPosts = $relatedPosts->merge($tagsPosts);
        }
        
        // Priority 3: Same author (if need more posts)
        if ($relatedPosts->count() < $limit) {
            $authorPosts = static::where('author', $this->author)
                ->where('id', '!=', $this->id)
                ->where('is_published', true)
                ->whereNotIn('id', $relatedPosts->pluck('id'))
                ->latest()
                ->take($limit - $relatedPosts->count())
                ->get();
                
            $relatedPosts = $relatedPosts->merge($authorPosts);
        }
        
        // Priority 4: Latest posts (if still need more)
        if ($relatedPosts->count() < $limit) {
            $latestPosts = static::where('id', '!=', $this->id)
                ->where('is_published', true)
                ->whereNotIn('id', $relatedPosts->pluck('id'))
                ->latest()
                ->take($limit - $relatedPosts->count())
                ->get();
                
            $relatedPosts = $relatedPosts->merge($latestPosts);
        }
        
        return $relatedPosts->take($limit);
    }
}
