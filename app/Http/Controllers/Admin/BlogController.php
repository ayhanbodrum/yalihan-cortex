<?php

namespace App\Http\Controllers\Admin;

use App\Models\BlogComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BlogController extends AdminController
{
    public function index()
    {
        return redirect()->route('admin.blog.comments.index');
    }

    public function comments(Request $request)
    {
        $status = $request->get('status');
        $query = BlogComment::with(['post:id,title,slug', 'user:id,name,email'])->orderByDesc('created_at');
        if ($status) {
            $query->where('status', $status);
        }
        $comments = $query->paginate(20)->withQueryString();

        // ✅ Context7: View için gerekli değişkenler
        // ✅ N+1 FIX: Select optimization for posts dropdown
        $posts = \App\Models\BlogPost::select(['id', 'title', 'slug'])
            ->orderBy('title')
            ->get();

        // ✅ CACHE: İstatistikler cache ile optimize et (1800s = 30 dakika)
        $stats = Cache::remember('blog_comments_stats', 1800, function () {
            return [
                'approved' => BlogComment::where('status', 'approved')->count(),
                'pending' => BlogComment::where('status', 'pending')->count(),
                'rejected' => BlogComment::where('status', 'rejected')->count(),
                'spam' => BlogComment::where('status', 'spam')->count(),
            ];
        });

        return view('admin.blog.comments.index', compact('comments', 'posts', 'stats'));
    }

    public function approveComment(BlogComment $comment)
    {
        $comment->approve(Auth::id());
        // ✅ CACHE INVALIDATION: İstatistik cache'ini temizle
        Cache::forget('blog_comments_stats');
        return response()->json(['success' => true]);
    }

    public function rejectComment(Request $request, BlogComment $comment)
    {
        $reason = $request->input('reason');
        $comment->reject(Auth::id(), $reason);
        // ✅ CACHE INVALIDATION: İstatistik cache'ini temizle
        Cache::forget('blog_comments_stats');
        return response()->json(['success' => true]);
    }

    public function markCommentAsSpam(Request $request, BlogComment $comment)
    {
        $reason = $request->input('reason');
        $comment->markAsSpam(Auth::id(), $reason);
        // ✅ CACHE INVALIDATION: İstatistik cache'ini temizle
        Cache::forget('blog_comments_stats');
        return response()->json(['success' => true]);
    }

    public function categories(Request $request)
    {
        $categories = \App\Models\BlogCategory::withCount('posts')->orderBy('name')->paginate(20);

        // ✅ CACHE: İstatistikler cache ile optimize et (3600s = 1 saat)
        $istatistikler = Cache::remember('blog_categories_stats', 3600, function () {
            return [
                'toplam' => \App\Models\BlogCategory::count(),
                'aktif' => \App\Models\BlogCategory::where('status', true)->count(), // ✅ Context7: status boolean
            ];
        });

        return view('admin.blog.categories.index', compact('categories', 'istatistikler'));
    }

    public function tags(Request $request)
    {
        $tags = \App\Models\BlogTag::withCount('posts')->orderBy('name')->paginate(20);

        // ✅ CACHE: İstatistikler cache ile optimize et (3600s = 1 saat)
        $istatistikler = Cache::remember('blog_tags_stats', 3600, function () {
            return [
                'toplam' => \App\Models\BlogTag::count(),
                'aktif' => \App\Models\BlogTag::where('status', true)->count(), // ✅ Context7: status boolean
            ];
        });

        return view('admin.blog.tags.index', compact('tags', 'istatistikler'));
    }

    public function posts(Request $request)
    {
        // ✅ N+1 FIX: Eager loading with select optimization
        $posts = \App\Models\BlogPost::with([
            'category:id,name,slug',
            'author:id,name,email'
        ])
        ->select(['id', 'title', 'slug', 'status', 'category_id', 'author_id', 'created_at', 'updated_at'])
        ->latest()
        ->paginate(20);

        // ✅ CACHE: İstatistikler cache ile optimize et (1800s = 30 dakika)
        $istatistikler = Cache::remember('blog_posts_stats', 1800, function () {
            return [
                'toplam' => \App\Models\BlogPost::count(),
                'yayinlanan' => \App\Models\BlogPost::where('status', 'published')->count(),
                'taslak' => \App\Models\BlogPost::where('status', 'draft')->count(),
            ];
        });

        // ✅ Context7: View için gerekli değişkenler
        $status = $request->get('status'); // Filter için
        $taslak = $istatistikler['taslak']; // View'da kullanılıyor

        return view('admin.blog.posts.index', compact('posts', 'istatistikler', 'status', 'taslak'));
    }
}
