<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class NotificationController extends AdminController
{
    /**
     * Display notifications index page
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): \Illuminate\View\View
    {
        return view('admin.notifications.index');
    }

    /**
     * Test real-time notification endpoint
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function testRealTime(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Test notification sent successfully'
            ]);
        }

        return view('admin.notifications.index')->with('success', 'Test notification sent');
    }

    /**
     * Test SMS notification endpoint
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testSms(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'SMS test notification sent successfully'
        ]);
    }

    /**
     * Test email notification endpoint
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testEmail(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Email test notification sent successfully'
        ]);
    }

    /**
     * Get notification statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total' => 0,
                'unread' => 0,
                'read' => 0
            ]
        ]);
    }

    /**
     * Get unread notification count
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadCount()
    {
        return response()->json([
            'success' => true,
            'count' => 0
        ]);
    }

    /**
     * Get recent notifications
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function recent()
    {
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }

    /**
     * Show the form for creating a new notification
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.notifications.create');
    }

    /**
     * Store a newly created notification
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|in:success,warning,error,info',
            'priority' => 'nullable|in:urgent,high,normal,low',
            'user_id' => 'nullable|exists:users,id',
            'role' => 'nullable|string',
        ]);

        $notification = \App\Models\Notification::create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'type' => $validated['type'] ?? 'info',
            'priority' => $validated['priority'] ?? 'normal',
            'user_id' => $validated['user_id'] ?? auth()->id(),
            'role' => $validated['role'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Bildirim başarıyla oluşturuldu');
    }

    /**
     * Display the specified notification
     *
     * @param \App\Models\Notification $notification
     * @return \Illuminate\View\View
     */
    public function show(\App\Models\Notification $notification)
    {
        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Remove the specified notification
     *
     * @param \App\Models\Notification $notification
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(\App\Models\Notification $notification)
    {
        $notification->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bildirim başarıyla silindi'
            ]);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Bildirim başarıyla silindi');
    }

    /**
     * Mark notification as read
     *
     * @param \App\Models\Notification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(\App\Models\Notification $notification)
    {
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Bildirim okundu olarak işaretlendi'
        ]);
    }

    /**
     * Mark notification as unread
     *
     * @param \App\Models\Notification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsUnread(\App\Models\Notification $notification)
    {
        $notification->markAsUnread();

        return response()->json([
            'success' => true,
            'message' => 'Bildirim okunmadı olarak işaretlendi'
        ]);
    }

    /**
     * Mark all notifications as read
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        \App\Models\Notification::whereNull('read_at')
            ->where('user_id', auth()->id())
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Tüm bildirimler okundu olarak işaretlendi'
        ]);
    }
}
