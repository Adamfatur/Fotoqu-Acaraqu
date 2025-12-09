<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoSession;
use App\Models\Photobox;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, $id)
    {
        // Store notification as read in session
        session(['notification_read_' . $id => true]);
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        // Get all current notifications and mark them as read
        $pendingSessions = PhotoSession::where(function($query) {
            $query->where('payment_status', 'pending')
                  ->orWhere('session_status', 'created');
        })->get();
        
        $recentSessions = PhotoSession::whereIn('session_status', ['completed'])->get();
        $photoboxUpdates = Photobox::where('updated_at', '>=', now()->subHours(2))->get();
        
        // Mark all as read in session
        foreach ($pendingSessions as $session) {
            session(['notification_read_session_pending_' . $session->id => true]);
        }
        
        foreach ($recentSessions as $session) {
            session(['notification_read_session_completed_' . $session->id => true]);
        }
        
        foreach ($photoboxUpdates as $photobox) {
            session(['notification_read_photobox_update_' . $photobox->id => true]);
        }
        
        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        // Get count of unread notifications
        $pendingSessionsCount = PhotoSession::where(function($query) {
            $query->where('payment_status', 'pending')
                  ->orWhere('session_status', 'created');
        })->count();
        
        $recentUpdatesCount = Photobox::where('updated_at', '>=', now()->subHours(1))->count();
        
        // Calculate unread count by checking session storage
        $unreadCount = 0;
        
        // Check pending sessions
        $pendingSessions = PhotoSession::where(function($query) {
            $query->where('payment_status', 'pending')
                  ->orWhere('session_status', 'created');
        })->get();
        
        foreach ($pendingSessions as $session) {
            if (!session('notification_read_session_pending_' . $session->id, false)) {
                $unreadCount++;
            }
        }
        
        // Check completed sessions
        $recentSessions = PhotoSession::whereIn('session_status', ['completed'])
            ->where('updated_at', '>=', now()->subHours(2))
            ->get();
            
        foreach ($recentSessions as $session) {
            if (!session('notification_read_session_completed_' . $session->id, false)) {
                $unreadCount++;
            }
        }
        
        // Check photobox updates
        $photoboxUpdates = Photobox::where('updated_at', '>=', now()->subHours(1))->get();
        
        foreach ($photoboxUpdates as $photobox) {
            if (!session('notification_read_photobox_update_' . $photobox->id, false)) {
                $unreadCount++;
            }
        }
        
        return response()->json([
            'count' => $unreadCount
        ]);
    }

    public function getNotifications()
    {
        $notifications = [];
        
        // Get pending sessions
        $pendingSessions = PhotoSession::with(['photobox'])
            ->where(function($query) {
                $query->where('payment_status', 'pending')
                      ->orWhere('session_status', 'created');
            })
            ->latest()
            ->take(5)
            ->get();
        
        foreach ($pendingSessions as $session) {
            $isRead = session('notification_read_session_pending_' . $session->id, false);
            $notifications[] = [
                'id' => 'session_pending_' . $session->id,
                'type' => 'pending_session',
                'title' => 'Sesi Menunggu Pembayaran',
                'message' => "Sesi {$session->session_code} dari {$session->customer_name} menunggu konfirmasi pembayaran",
                'time' => $session->created_at,
                'read' => $isRead,
                'action_url' => route('admin.sessions.show', $session->id)
            ];
        }
        
        // Get completed sessions in last 2 hours
        $recentSessions = PhotoSession::with(['photobox'])
            ->whereIn('session_status', ['completed'])
            ->where('updated_at', '>=', now()->subHours(2))
            ->latest()
            ->take(5)
            ->get();
            
        foreach ($recentSessions as $session) {
            $isRead = session('notification_read_session_completed_' . $session->id, false);
            $notifications[] = [
                'id' => 'session_completed_' . $session->id,
                'type' => 'completed_session',
                'title' => 'Sesi Selesai',
                'message' => "Sesi {$session->session_code} telah selesai dan frame berhasil dibuat",
                'time' => $session->updated_at,
                'read' => $isRead,
                'action_url' => route('admin.sessions.show', $session->id)
            ];
        }
        
        // Get photobox updates in last hour
        $photoboxUpdates = Photobox::where('updated_at', '>=', now()->subHours(1))
            ->latest()
            ->take(3)
            ->get();
        
        foreach ($photoboxUpdates as $photobox) {
            $isRead = session('notification_read_photobox_update_' . $photobox->id, false);
            $notifications[] = [
                'id' => 'photobox_update_' . $photobox->id,
                'type' => 'photobox_update',
                'title' => 'Update Photobox',
                'message' => "Photobox {$photobox->code} telah diperbarui",
                'time' => $photobox->updated_at,
                'read' => $isRead,
                'action_url' => route('admin.photoboxes.show', $photobox->id)
            ];
        }
        
        // Sort by time (newest first)
        usort($notifications, function($a, $b) {
            return $b['time']->timestamp - $a['time']->timestamp;
        });
        
        return response()->json([
            'notifications' => array_slice($notifications, 0, 10) // Limit to 10 notifications
        ]);
    }
}
