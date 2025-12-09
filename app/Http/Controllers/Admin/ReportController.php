<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotoSession;
use App\Models\Package;
use App\Models\Photobox;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Get date range and payment method filter from request
        $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->endOfMonth()->format('Y-m-d'));
        $paymentMethod = request('payment_method'); // null, 'free', 'qris', 'edc'
        // Normalize to full-day bounds to include entire end date
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Basic stats with payment method filter
        $sessionsQuery = PhotoSession::whereBetween('created_at', [$start, $end])
            ->whereNull('photobooth_event_id');

        if ($paymentMethod) {
            $sessionsQuery->whereHas('paymentLogs', function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            });
        }

        $totalSessions = $sessionsQuery->count();
        $completedSessions = (clone $sessionsQuery)->where('session_status', 'completed')->count();

        // Revenue calculation based on filter
        $revenueQuery = PhotoSession::whereBetween('created_at', [$start, $end])
            ->whereNull('photobooth_event_id')
            ->where('payment_status', 'paid');

        if ($paymentMethod) {
            $revenueQuery->whereHas('paymentLogs', function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            });
        }

        $totalRevenue = $revenueQuery->sum('total_price');
        $averageOrderValue = $totalSessions > 0 ? $totalRevenue / $totalSessions : 0;

        // Payment method breakdown
        $paymentMethodStats = PhotoSession::whereBetween('photo_sessions.created_at', [$start, $end])
            ->whereNull('photo_sessions.photobooth_event_id')
            ->join('payment_logs', 'photo_sessions.id', '=', 'payment_logs.photo_session_id')
            ->where('payment_logs.status', 'completed')
            ->groupBy('payment_logs.payment_method')
            ->selectRaw('payment_logs.payment_method as method, COUNT(*) as count, SUM(payment_logs.amount) as revenue')
            ->get();

        // Daily sessions chart data with payment method filter
        $dailySessionsQuery = PhotoSession::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$start, $end])
            ->whereNull('photobooth_event_id');

        if ($paymentMethod) {
            $dailySessionsQuery->whereHas('paymentLogs', function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            });
        }

        $dailySessions = $dailySessionsQuery->groupBy('date')->orderBy('date')->get();

        // Revenue by package with payment method filter
        $packageRevenueQuery = PhotoSession::select('packages.name', DB::raw('SUM(photo_sessions.total_price) as revenue'), DB::raw('COUNT(*) as sessions'))
            ->join('packages', 'photo_sessions.package_id', '=', 'packages.id')
            ->whereBetween('photo_sessions.created_at', [$start, $end])
            ->whereNull('photo_sessions.photobooth_event_id')
            ->where('photo_sessions.payment_status', 'paid');

        if ($paymentMethod) {
            $packageRevenueQuery->whereHas('paymentLogs', function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            });
        }

        $packageRevenue = $packageRevenueQuery->groupBy('packages.id', 'packages.name')
            ->orderBy('revenue', 'desc')
            ->get();

        // Photobox performance with payment method filter
        $photoboxQuery = PhotoSession::select('photoboxes.code', 'photoboxes.name', DB::raw('COUNT(*) as sessions'), DB::raw('SUM(photo_sessions.total_price) as revenue'))
            ->join('photoboxes', 'photo_sessions.photobox_id', '=', 'photoboxes.id')
            ->whereBetween('photo_sessions.created_at', [$start, $end])
            ->whereNull('photo_sessions.photobooth_event_id');

        if ($paymentMethod) {
            $photoboxQuery->whereHas('paymentLogs', function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            });
        }

        $photoboxPerformance = $photoboxQuery->groupBy('photoboxes.id', 'photoboxes.code', 'photoboxes.name')
            ->orderBy('sessions', 'desc')
            ->get();

        // Top customers with payment method filter
        $customersQuery = PhotoSession::select('customer_name', 'customer_email', DB::raw('COUNT(*) as sessions'), DB::raw('SUM(total_price) as total_spent'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNull('photobooth_event_id')
            ->where('payment_status', 'paid');

        if ($paymentMethod) {
            $customersQuery->whereHas('paymentLogs', function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            });
        }

        $topCustomers = $customersQuery->groupBy('customer_name', 'customer_email')
            ->orderBy('total_spent', 'desc')
            ->take(5)
            ->get();

        // Recent sessions with payment method information
        $recentSessionsQuery = PhotoSession::with([
            'package',
            'photobox',
            'paymentLogs' => function ($query) {
                $query->where('status', 'completed')->latest();
            }
        ])
            ->whereBetween('created_at', [$start, $end])
            ->whereNull('photobooth_event_id');

        if ($paymentMethod) {
            $recentSessionsQuery->whereHas('paymentLogs', function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            });
        }

        $recentSessions = $recentSessionsQuery->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.reports.index', compact(
            'totalSessions',
            'completedSessions',
            'totalRevenue',
            'averageOrderValue',
            'paymentMethodStats',
            'dailySessions',
            'packageRevenue',
            'photoboxPerformance',
            'topCustomers',
            'recentSessions',
            'startDate',
            'endDate',
            'paymentMethod'
        ));
    }

    public function export(Request $request)
    {
        $format = $request->input('format', 'excel'); // excel, pdf, csv
        $type = $request->input('type', 'sessions'); // sessions, revenue, packages
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $paymentMethod = $request->input('payment_method');
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        switch ($type) {
            case 'sessions':
                return $this->exportSessions($format, $start, $end, $paymentMethod);
            case 'revenue':
                return $this->exportRevenue($format, $start, $end, $paymentMethod);
            case 'packages':
                return $this->exportPackages($format, $start, $end, $paymentMethod);
            default:
                return back()->with('error', 'Tipe laporan tidak valid');
        }
    }

    private function exportSessions($format, $start, $end, $paymentMethod = null)
    {
        $sessions = PhotoSession::with([
            'user',
            'package',
            'photobox',
            'paymentLogs' => function ($q) {
                $q->where('status', 'completed')->latest();
            }
        ])
            ->whereBetween('created_at', [$start, $end])
            ->whereNull('photobooth_event_id')
            ->when($paymentMethod, function ($q) use ($paymentMethod) {
                $q->whereHas('paymentLogs', function ($qq) use ($paymentMethod) {
                    $qq->where('payment_method', $paymentMethod);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = "laporan-sesi-" . $start->format('Y-m-d') . "-to-" . $end->format('Y-m-d');

        if ($format === 'csv') {
            return $this->exportToCsv($sessions, $filename, [
                'Kode Sesi',
                'Tanggal',
                'Customer',
                'Email',
                'Paket',
                'Photobox',
                'Status Sesi',
                'Metode Pembayaran',
                'Status Pembayaran',
                'Total (IDR)'
            ], function ($session) {
                $paymentLog = $session->paymentLogs->first();
                $method = $paymentLog->payment_method ?? '';
                return [
                    $session->session_code,
                    $session->created_at->format('Y-m-d H:i'),
                    $session->customer_name,
                    $session->customer_email,
                    optional($session->package)->name ?? '-',
                    optional($session->photobox)->code ?? '-',
                    $session->session_status,
                    $method ?: '-',
                    $session->payment_status,
                    (int) $session->total_price
                ];
            });
        }

        // Default to JSON for now (you can implement Excel/PDF later)
        return response()->json($sessions);
    }

    private function exportRevenue($format, $start, $end, $paymentMethod = null)
    {
        $revenue = PhotoSession::selectRaw('
                DATE(created_at) as date,
                COUNT(*) as total_sessions,
                SUM(CASE WHEN payment_status = "paid" THEN total_price ELSE 0 END) as paid_revenue,
                SUM(total_price) as total_revenue
            ')
            ->whereBetween('created_at', [$start, $end])
            ->whereNull('photobooth_event_id')
            ->when($paymentMethod, function ($q) use ($paymentMethod) {
                $q->whereHas('paymentLogs', function ($qq) use ($paymentMethod) {
                    $qq->where('payment_method', $paymentMethod);
                });
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $filename = "laporan-revenue-" . $start->format('Y-m-d') . "-to-" . $end->format('Y-m-d');

        if ($format === 'csv') {
            return $this->exportToCsv($revenue, $filename, [
                'Tanggal',
                'Total Sesi',
                'Revenue Terbayar (IDR)',
                'Total Revenue (IDR)'
            ], function ($item) {
                return [
                    $item->date,
                    $item->total_sessions,
                    (int) $item->paid_revenue,
                    (int) $item->total_revenue
                ];
            });
        }

        return response()->json($revenue);
    }

    private function exportPackages($format, $start, $end, $paymentMethod = null)
    {
        $packages = Package::withCount([
            'photoSessions' => function ($query) use ($start, $end, $paymentMethod) {
                $query->whereBetween('created_at', [$start, $end])
                    ->whereNull('photobooth_event_id')
                    ->when($paymentMethod, function ($q) use ($paymentMethod) {
                        $q->whereHas('paymentLogs', function ($qq) use ($paymentMethod) {
                            $qq->where('payment_method', $paymentMethod);
                        });
                    });
            }
        ])
            ->withSum([
                'photoSessions' => function ($query) use ($start, $end, $paymentMethod) {
                    $query->whereBetween('created_at', [$start, $end])
                        ->whereNull('photobooth_event_id')
                        ->where('payment_status', 'paid')
                        ->when($paymentMethod, function ($q) use ($paymentMethod) {
                            $q->whereHas('paymentLogs', function ($qq) use ($paymentMethod) {
                                $qq->where('payment_method', $paymentMethod);
                            });
                        });
                }
            ], 'total_price')
            ->get();

        $filename = "laporan-paket-" . $start->format('Y-m-d') . "-to-" . $end->format('Y-m-d');

        if ($format === 'csv') {
            return $this->exportToCsv($packages, $filename, [
                'Nama Paket',
                'Harga (IDR)',
                'Harga Diskon (IDR)',
                'Total Sesi',
                'Total Revenue (IDR)',
                'Status'
            ], function ($package) {
                return [
                    $package->name,
                    (int) $package->price,
                    $package->discount_price ? (int) $package->discount_price : 0,
                    $package->photo_sessions_count,
                    (int) ($package->photo_sessions_sum_total_price ?? 0),
                    $package->is_active ? 'Aktif' : 'Nonaktif'
                ];
            });
        }

        return response()->json($packages);
    }

    private function exportToCsv($data, $filename, $headers, $rowCallback)
    {
        $filename .= '.csv';

        $handle = fopen('php://temp', 'w+');

        // Add BOM for UTF-8
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Add headers
        fputcsv($handle, $headers);

        // Add data
        foreach ($data as $item) {
            fputcsv($handle, $rowCallback($item));
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
