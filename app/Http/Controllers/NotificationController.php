<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /* ── Index ── */
    public function index(Request $request)
    {
        $user          = $request->user();
        $notifications = $user->notifications()->paginate(30);
        $unreadCount   = $user->unreadNotifications()->count();

        return view('notifications.index', compact('user', 'notifications', 'unreadCount'));
    }

    /* ── Mark one read, stay on notifications page ── */
    public function markRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->route('notifications.index');
    }

    /* ── Mark all read ── */
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('status', 'All notifications marked as read.');
    }

    /* ── Delete one ── */
    public function destroy(Request $request, string $id)
    {
        $request->user()->notifications()->findOrFail($id)->delete();

        return back()->with('status', 'Notification deleted.');
    }

    /* ── Delete all ── */
    public function destroyAll(Request $request)
    {
        $request->user()->notifications()->delete();

        return back()->with('status', 'All notifications cleared.');
    }
}
