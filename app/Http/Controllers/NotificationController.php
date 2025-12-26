<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    /**
     * Display all notifications (Livewire datatable view)
     */
    public function index()
    {
        return view('notifications.index');
    }

    /**
     * Mark a notification as read (for header dropdown)
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notificación marcada como leída',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notificación no encontrada',
        ], 404);
    }

    /**
     * Mark all notifications as read (for header dropdown)
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Todas las notificaciones marcadas como leídas',
        ]);
    }

    /**
     * Get unread notifications count (for header dropdown)
     */
    public function getUnreadCount()
    {
        $count = auth()->user()->unreadNotifications()->count();

        return response()->json([
            'count' => $count,
        ]);
    }
}
