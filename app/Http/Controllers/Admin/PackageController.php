<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::withCount('photoSessions')->ordered()->get();
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'frame_slots' => 'required|integer|min:1',
            'print_type' => 'required|in:none,strip,custom',
            'print_count' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
            'is_featured' => 'nullable|boolean'
        ];

        if ($request->input('price') > 0) {
            $rules['discount_price'] .= '|lt:price';
        }

        $request->validate($rules);

        try {
            // Get the highest sort_order
            $maxSort = Package::max('sort_order') ?? 0;

            Package::create([
                'name' => $request->name,
                'description' => $request->description,
                'frame_slots' => $request->frame_slots,
                'print_type' => $request->print_type,
                'print_count' => $request->print_count,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'is_active' => $request->is_active,
                'is_featured' => $request->has('is_featured'),
                'sort_order' => $maxSort + 1
            ]);

            return redirect()
                ->route('admin.packages.index')
                ->with('success', 'Paket berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan paket: ' . $e->getMessage());
        }
    }

    public function edit(Package $package)
    {
        $package->loadCount('photoSessions');
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'frame_slots' => 'required|integer|min:1',
            'print_type' => 'required|in:none,strip,custom',
            'print_count' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
            'is_featured' => 'nullable|boolean'
        ];

        if ($request->input('price') > 0) {
            $rules['discount_price'] .= '|lt:price';
        }

        $request->validate($rules);

        try {
            $package->update([
                'name' => $request->name,
                'description' => $request->description,
                'frame_slots' => $request->frame_slots,
                'print_type' => $request->print_type,
                'print_count' => $request->print_count,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'is_active' => $request->is_active,
                'is_featured' => $request->has('is_featured')
            ]);

            return redirect()
                ->route('admin.packages.index')
                ->with('success', 'Paket berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui paket: ' . $e->getMessage());
        }
    }

    public function destroy(Package $package)
    {
        try {
            // Check if package is being used
            if ($package->photoSessions()->count() > 0) {
                return back()->with('error', 'Tidak dapat menghapus paket yang sedang digunakan!');
            }

            $package->delete();

            return redirect()
                ->route('admin.packages.index')
                ->with('success', 'Paket berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus paket: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Package $package)
    {
        try {
            $package->update(['is_active' => !$package->is_active]);

            return response()->json([
                'success' => true,
                'is_active' => $package->is_active,
                'message' => 'Status paket berhasil diubah!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }
}
