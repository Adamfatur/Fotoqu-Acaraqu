<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PhotoboothEventController extends Controller
{
    public function index()
    {
        $events = \App\Models\PhotoboothEvent::with('package')
            ->orderBy('status', 'asc') // Active first
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $packages = \App\Models\Package::where('is_active', true)->get();
        $photoboxes = \App\Models\Photobox::where('status', 'active')->get();

        return view('admin.events.create', compact('packages', 'photoboxes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'package_id' => 'required|exists:packages,id',
            'photobox_id' => 'required|exists:photoboxes,id',
            'duration_minutes' => 'required|integer|min:1',
            'print_quota' => 'nullable|integer|min:0',
            'event_type' => 'required|in:digital,print'
        ]);

        // Double check active event for this photobox
        if (\App\Models\PhotoboothEvent::active()->where('photobox_id', $request->photobox_id)->exists()) {
            return back()->with('error', 'Photobox ini sedang menjalankan event lain.');
        }

        // Calculate active_until
        $activeUntil = now()->addMinutes((int) $request->duration_minutes);

        // If digital only, force print_quota to null (unlimited digital) or 0? 
        // Actually, for digital only, print_quota is irrelevant. 
        // But if user selected 'print' type, they might have set a quota.

        $printQuota = $request->print_quota;
        if ($request->event_type === 'digital') {
            $printQuota = 0; // No printing allowed
        }

        $event = \App\Models\PhotoboothEvent::create([
            'name' => $request->name,
            'package_id' => $request->package_id,
            'photobox_id' => $request->photobox_id,
            'status' => 'active',
            'print_quota' => $printQuota,
            'active_from' => now(),
            'active_until' => $activeUntil
        ]);

        // Create the first session immediately
        \App\Models\PhotoSession::create([
            'photobox_id' => $event->photobox_id,
            'photobooth_event_id' => $event->id,
            'package_id' => $event->package_id,
            'admin_id' => auth()->id() ?? 1,
            'customer_name' => 'Event Guest',
            'customer_email' => 'guest@event.com',
            'frame_slots' => $event->package->frame_slots ?? 6,
            'session_status' => 'approved', // Ready to start
            'payment_status' => 'paid',
            'total_price' => 0,
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dimulai! Berakhir pada ' . $activeUntil->format('H:i'));
    }

    public function show(\App\Models\PhotoboothEvent $event)
    {
        $event->load([
            'package',
            'photoSessions' => function ($q) {
                $q->latest()->take(10);
            }
        ]);

        // Calculate stats
        $totalSessions = $event->photoSessions()->count();
        $completedSessions = $event->photoSessions()->where('session_status', 'completed')->count();

        return view('admin.events.show', compact('event', 'totalSessions', 'completedSessions'));
    }

    public function stop(\App\Models\PhotoboothEvent $event)
    {
        if ($event->status !== 'active') {
            return back()->with('error', 'Event sudah tidak aktif.');
        }

        $event->update([
            'status' => 'completed',
            'active_until' => now()
        ]);

        // Cancel any pending/approved sessions for this event
        $event->photoSessions()
            ->where('session_status', 'approved')
            ->update(['session_status' => 'cancelled']);

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihentikan.');
    }

    public function createSession(\App\Models\PhotoboothEvent $event)
    {
        if ($event->status !== 'active') {
            return back()->with('error', 'Event tidak aktif.');
        }

        \App\Models\PhotoSession::create([
            'photobox_id' => $event->photobox_id,
            'photobooth_event_id' => $event->id,
            'package_id' => $event->package_id,
            'admin_id' => auth()->id() ?? 1, // Fallback to 1 if not logged in (should be logged in)
            'customer_name' => 'Event Guest',
            'customer_email' => 'guest@event.com',
            'frame_slots' => $event->package->frame_slots ?? 6,
            'session_status' => 'approved',
            'payment_status' => 'paid',
            'total_price' => 0,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Sesi baru berhasil dibuat secara manual.');
    }
}
