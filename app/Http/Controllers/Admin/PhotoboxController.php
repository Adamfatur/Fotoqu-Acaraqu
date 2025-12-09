<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photobox;
use App\Models\PhotoboxAccessToken;
use Illuminate\Http\Request;

class PhotoboxController extends Controller
{
    public function index(Request $request)
    {
        $query = Photobox::with(['activePhotoSessions', 'activeAccessToken'])
            ->withCount('activePhotoSessions');

        // Filter by status if specified
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $photoboxes = $query->orderBy('created_at', 'desc')->get();

        return view('admin.photoboxes.index', compact('photoboxes'));
    }

    public function create()
    {
        return view('admin.photoboxes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:photoboxes,code',
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string|max:500'
        ]);

        try {
            Photobox::create([
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'location' => $request->location,
                'description' => $request->description,
                'status' => 'active'
            ]);

            return redirect()
                ->route('admin.photoboxes.index')
                ->with('success', 'Photobox berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan photobox: ' . $e->getMessage());
        }
    }

    public function edit(Photobox $photobox)
    {
        return view('admin.photoboxes.edit', compact('photobox'));
    }

    /**
     * Display the specified photobox.
     * For now, redirect to the edit page which serves as the detail view.
     */
    public function show(Photobox $photobox)
    {
        return redirect()->route('admin.photoboxes.edit', $photobox);
    }

    public function update(Request $request, Photobox $photobox)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:photoboxes,code,' . $photobox->id,
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive,maintenance'
        ]);

        try {
            $photobox->update([
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'location' => $request->location,
                'description' => $request->description,
                'status' => $request->status
            ]);

            return redirect()
                ->route('admin.photoboxes.index')
                ->with('success', 'Photobox berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui photobox: ' . $e->getMessage());
        }
    }

    public function destroy(Photobox $photobox)
    {
        try {
            // Check if photobox has active sessions
            if ($photobox->activePhotoSessions()->count() > 0) {
                return back()->with('error', 'Tidak dapat menghapus photobox yang memiliki sesi aktif!');
            }

            $photobox->delete();

            return redirect()
                ->route('admin.photoboxes.index')
                ->with('success', 'Photobox berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus photobox: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Photobox $photobox)
    {
        try {
            // Fix: Use correct enum values from migration - 'active', 'inactive', 'maintenance'
            $newStatus = $photobox->status === 'active' ? 'maintenance' : 'active';
            $photobox->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => 'Status photobox berhasil diubah!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateAccessToken(Photobox $photobox)
    {
        try {
            // Reuse existing valid token if available
            $existing = $photobox->activeAccessToken()->first();
            if ($existing) {
                $url = route('photobox.show', ['photobox' => $photobox->code]) . '?token=' . $existing->token;
                return response()->json([
                    'success' => true,
                    'token' => $existing->token,
                    'expires_at' => $existing->expires_at->toIso8601String(),
                    'url' => $url,
                    'reused' => true,
                ]);
            }

            $token = bin2hex(random_bytes(32)); // 64 hex chars
            $expiresAt = now()->addDay();

            $record = PhotoboxAccessToken::create([
                'photobox_id' => $photobox->id,
                'token' => $token,
                'expires_at' => $expiresAt,
                'created_by' => auth()->id(),
            ]);

            $url = route('photobox.show', ['photobox' => $photobox->code]) . '?token=' . $token;

            return response()->json([
                'success' => true,
                'token' => $token,
                'expires_at' => $expiresAt->toIso8601String(),
                'url' => $url,
                'reused' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat token akses: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function revokeAccessToken(Photobox $photobox, Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $token = PhotoboxAccessToken::where('photobox_id', $photobox->id)
            ->where('token', $request->input('token'))
            ->first();

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token tidak ditemukan'], 404);
        }

        $token->update(['revoked_at' => now()]);

        return response()->json(['success' => true]);
    }
}
