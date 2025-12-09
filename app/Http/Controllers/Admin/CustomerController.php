<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PhotoSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::customers()->with(['photoSessions' => function($q) {
            $q->latest()->limit(1);
        }]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by session count
        if ($request->filled('sessions')) {
            if ($request->sessions === 'has_sessions') {
                $query->whereHas('photoSessions');
            } elseif ($request->sessions === 'no_sessions') {
                $query->whereDoesntHave('photoSessions');
            }
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $customers = $query->withCount('photoSessions')
            ->latest()
            ->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        if (!$customer->isCustomer()) {
            abort(404);
        }

        $photoSessions = $customer->photoSessions()
            ->with(['photobox', 'frame', 'package'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total_sessions' => $customer->photoSessions()->count(),
            'completed_sessions' => $customer->photoSessions()->where('session_status', 'completed')->count(),
            'total_spent' => $customer->photoSessions()->where('payment_status', 'paid')->sum('total_price'),
            'last_session' => $customer->photoSessions()->latest()->first()?->created_at,
        ];

        return view('admin.customers.show', compact('customer', 'photoSessions', 'stats'));
    }

    public function export(Request $request)
    {
        $query = User::customers()->with(['photoSessions' => function($q) {
            $q->latest()->limit(1);
        }]);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('sessions')) {
            if ($request->sessions === 'has_sessions') {
                $query->whereHas('photoSessions');
            } elseif ($request->sessions === 'no_sessions') {
                $query->whereDoesntHave('photoSessions');
            }
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $customers = $query->withCount('photoSessions')->get();

        $filename = 'customers_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        return Response::stream(function() use ($customers) {
            $handle = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($handle, [
                'ID',
                'Nama',
                'Email', 
                'Telepon',
                'Jumlah Sesi',
                'Tanggal Daftar',
                'Sesi Terakhir',
                'Status'
            ]);

            // CSV Data
            foreach ($customers as $customer) {
                fputcsv($handle, [
                    $customer->id,
                    $customer->name,
                    $customer->email,
                    $customer->phone ?? '-',
                    $customer->photo_sessions_count,
                    $customer->created_at->format('Y-m-d H:i:s'),
                    $customer->photoSessions->first()?->created_at?->format('Y-m-d H:i:s') ?? '-',
                    $customer->status
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function emailList(Request $request)
    {
        $query = User::customers();

        // Filter only customers with sessions if requested
        if ($request->filled('with_sessions') && $request->with_sessions == '1') {
            $query->whereHas('photoSessions');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $emails = $query->pluck('email')->filter()->unique()->values();

        if ($request->filled('format') && $request->format === 'text') {
            return response($emails->implode("\n"), 200, [
                'Content-Type' => 'text/plain',
                'Content-Disposition' => 'attachment; filename="customer_emails.txt"'
            ]);
        }

        return response()->json([
            'emails' => $emails,
            'count' => $emails->count()
        ]);
    }
}
