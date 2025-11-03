<?php

namespace App\Http\Controllers\Admin;

use App\Models\BlogComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return view('admin.blog.comments.index', compact('comments'));
    }

    public function approveComment(BlogComment $comment)
    {
        $comment->approve(Auth::id());
        return response()->json(['success' => true]);
    }

    public function rejectComment(Request $request, BlogComment $comment)
    {
        $reason = $request->input('reason');
        $comment->reject(Auth::id(), $reason);
        return response()->json(['success' => true]);
    }

    public function markCommentAsSpam(Request $request, BlogComment $comment)
    {
        $reason = $request->input('reason');
        $comment->markAsSpam(Auth::id(), $reason);
        return response()->json(['success' => true]);
    }

    public function categories(Request $request)
    {
        $categories = \App\Models\BlogCategory::withCount('posts')->orderBy('name')->paginate(20);
        $istatistikler = [
            'toplam' => \App\Models\BlogCategory::count(),
            'aktif' => \App\Models\BlogCategory::where('status', 'Aktif')->count(),
        ];
        
        return view('admin.blog.categories.index', compact('categories', 'istatistikler'));
    }

    public function tags(Request $request)
    {
        $tags = \App\Models\BlogTag::withCount('posts')->orderBy('name')->paginate(20);
        $istatistikler = [
            'toplam' => \App\Models\BlogTag::count(),
            'aktif' => \App\Models\BlogTag::where('status', 'Aktif')->count(),
        ];
        
        return view('admin.blog.tags.index', compact('tags', 'istatistikler'));
    }

    public function posts(Request $request)
    {
        $posts = \App\Models\BlogPost::with(['category', 'author'])->latest()->paginate(20);
        $istatistikler = [
            'toplam' => \App\Models\BlogPost::count(),
            'yayinlanan' => \App\Models\BlogPost::where('status', 'published')->count(),
            'taslak' => \App\Models\BlogPost::where('status', 'draft')->count(),
        ];
        
        return view('admin.blog.posts.index', compact('posts', 'istatistikler'));
    }
}


