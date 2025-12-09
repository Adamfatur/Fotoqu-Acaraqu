<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FrameTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FrameTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = FrameTemplate::latest()->paginate(12);

        $stats = [
            'total' => FrameTemplate::count(),
            'active' => FrameTemplate::where('status', 'active')->count(),
            'fotostrip_6_slots' => FrameTemplate::where('slots', '6')->count(), // Only 6 slots for KP 108 IN fotostrip
        ];

        return view('admin.frame-templates.index', compact('templates', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.frame-templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'slots' => 'required|in:2,4,6', // Supports 2, 4, 6 slots for KP 108 IN different layouts
            'template_image' => 'required|image|mimes:jpeg,png,jpg|max:10240', // 10MB
            'preview_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB
            'background_color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'width' => 'required|integer|min:1000|max:1300', // KP 108 IN range at 300 DPI (100mm = 1181px)
            'height' => 'required|integer|min:1400|max:2000', // KP 108 IN range at 300 DPI (148mm = 1748px)
            'layout_config' => 'required|json',
            'is_default' => 'boolean',
            'is_recommended' => 'boolean',
        ]);

        // Handle template image upload
        $templatePath = $request->file('template_image')->store('frame-templates', 'public');

        // Handle preview image upload
        $previewPath = null;
        if ($request->hasFile('preview_image')) {
            $previewPath = $request->file('preview_image')->store('frame-templates/previews', 'public');
        }

        $template = FrameTemplate::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'slots' => $validated['slots'],
            'template_path' => $templatePath,
            'preview_path' => $previewPath,
            'layout_config' => json_decode($validated['layout_config'], true),
            'background_color' => $validated['background_color'],
            'width' => $validated['width'],
            'height' => $validated['height'],
            'is_default' => $request->boolean('is_default'),
            'is_recommended' => $request->boolean('is_recommended'),
        ]);

        // Set as default if requested
        if ($template->is_default) {
            $template->setAsDefault();
        }

        return redirect()->route('admin.frame-templates.index')
            ->with('success', 'Template frame berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(FrameTemplate $frame_template)
    {
        return view('admin.frame-templates.show', compact('frame_template'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FrameTemplate $frame_template)
    {
        return view('admin.frame-templates.edit', compact('frame_template'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FrameTemplate $frame_template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'slots' => ['required', Rule::in(['2', '4', '6'])], // Supports 2, 4, 6 slots
            'template_image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'preview_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'background_color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'width' => 'required|integer|min:1000|max:1300', // KP 108 IN range at 300 DPI (100mm = 1181px)
            'height' => 'required|integer|min:1400|max:2000', // KP 108 IN range at 300 DPI (148mm = 1748px)
            'layout_config' => 'required|json',
            'is_default' => 'boolean',
            'is_recommended' => 'boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'slots' => $validated['slots'],
            'background_color' => $validated['background_color'],
            'width' => $validated['width'],
            'height' => $validated['height'],
            'layout_config' => json_decode($validated['layout_config'], true),
            'is_default' => $request->boolean('is_default'),
            'is_recommended' => $request->boolean('is_recommended'),
        ];

        // Handle new template image
        if ($request->hasFile('template_image')) {
            // Delete old image
            if ($frame_template->template_path) {
                Storage::disk('public')->delete($frame_template->template_path);
            }
            $updateData['template_path'] = $request->file('template_image')->store('frame-templates', 'public');
        }

        // Handle new preview image
        if ($request->hasFile('preview_image')) {
            // Delete old preview
            if ($frame_template->preview_path) {
                Storage::disk('public')->delete($frame_template->preview_path);
            }
            $updateData['preview_path'] = $request->file('preview_image')->store('frame-templates/previews', 'public');
        }

        $frame_template->update($updateData);

        // Set as default if requested
        if ($frame_template->is_default) {
            $frame_template->setAsDefault();
        }

        return redirect()->route('admin.frame-templates.index')
            ->with('success', 'Template frame berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FrameTemplate $frame_template)
    {
        try {
            // Check if template is being used
            if ($frame_template->frames()->exists()) {
                if (request()->expectsJson()) {
                    return response()->json(['error' => 'Template tidak dapat dihapus karena sedang digunakan!'], 422);
                }
                return back()->with('error', 'Template tidak dapat dihapus karena sedang digunakan!');
            }

            // Delete template images
            if ($frame_template->template_path) {
                Storage::disk('public')->delete($frame_template->template_path);
            }
            if ($frame_template->preview_path) {
                Storage::disk('public')->delete($frame_template->preview_path);
            }

            $frame_template->delete();

            if (request()->expectsJson()) {
                return response()->json(['success' => 'Template frame berhasil dihapus!']);
            }

            return redirect()->route('admin.frame-templates.index')
                ->with('success', 'Template frame berhasil dihapus!');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Gagal menghapus template: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal menghapus template: ' . $e->getMessage());
        }
    }

    /**
     * Toggle template status.
     */
    public function toggleStatus(FrameTemplate $frame_template)
    {
        $newStatus = $frame_template->status === 'active' ? 'inactive' : 'active';
        $frame_template->update(['status' => $newStatus]);

        $message = $newStatus === 'active' ? 'Template diaktifkan!' : 'Template dinonaktifkan!';

        return response()->json([
            'success' => true,
            'message' => $message,
            'status' => $newStatus
        ]);
    }

    /**
     * Set template as default.
     */
    public function setDefault(FrameTemplate $frame_template)
    {
        $frame_template->setAsDefault();

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil dijadikan default!'
        ]);
    }

    /**
     * Download Photoshop design template with accurate measurements.
     */
    public function downloadDesignTemplate(Request $request)
    {
        $slots = $request->get('slots', '4'); // Default to 4 slots

        // Validate slots parameter
        if (!in_array($slots, ['2', '4', '6', '8'])) {
            $slots = '4';
        }

        return $this->generateDesignTemplate($slots);
    }

    /**
     * Generate Photoshop design template file.
     */
    private function generateDesignTemplate($slots)
    {
        // A5 dimensions at 300 DPI
        $canvasWidth = 1748;  // 148mm at 300 DPI
        $canvasHeight = 2480; // 210mm at 300 DPI

        // Layout configurations
        $layouts = [
            '2' => [
                'cols' => 1,
                'rows' => 2,
                'margin' => 80,
                'spacing' => 80,
                'bottom_area' => 220,
                'photo_width' => 1588,
                'photo_height' => 1050
            ],
            '4' => [
                'cols' => 2,
                'rows' => 2,
                'margin' => 60,
                'spacing' => 60,
                'bottom_area' => 200,
                'photo_width' => 784,
                'photo_height' => 1150
            ],
            '6' => [
                'cols' => 2,
                'rows' => 3,
                'margin' => 50,
                'spacing' => 50,
                'bottom_area' => 180,
                'photo_width' => 799,
                'photo_height' => 753
            ],
            '8' => [
                'cols' => 2,
                'rows' => 4,
                'margin' => 40,
                'spacing' => 40,
                'bottom_area' => 160,
                'photo_width' => 814,
                'photo_height' => 570
            ]
        ];

        $layout = $layouts[$slots];

        // Create SVG template with accurate measurements
        $svg = $this->createSVGTemplate($canvasWidth, $canvasHeight, $layout, $slots);

        // Create temporary file
        $tempPath = storage_path('app/temp/fotoku-design-template-' . $slots . '-slots.svg');

        // Ensure temp directory exists
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        file_put_contents($tempPath, $svg);

        $fileName = "Fotoku-Design-Template-{$slots}-Slots.svg";

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'image/svg+xml',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Create SVG template with photo placement guides.
     */
    private function createSVGTemplate($width, $height, $layout, $slots)
    {
        $svg = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $svg .= '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '" xmlns="http://www.w3.org/2000/svg">' . "\n";

        // Add styles
        $svg .= '<defs>' . "\n";
        $svg .= '<style>' . "\n";
        $svg .= '.photo-area { fill: #f3f4f6; stroke: #1e3a8a; stroke-width: 3; stroke-dasharray: 10,5; }' . "\n";
        $svg .= '.guide-line { stroke: #ef4444; stroke-width: 1; stroke-dasharray: 5,5; }' . "\n";
        $svg .= '.margin-line { stroke: #10b981; stroke-width: 2; stroke-dasharray: 3,3; }' . "\n";
        $svg .= '.text-label { font-family: Inter, Arial, sans-serif; font-size: 24px; font-weight: bold; fill: #1e3a8a; }' . "\n";
        $svg .= '.measurement { font-family: Inter, Arial, sans-serif; font-size: 18px; fill: #ef4444; }' . "\n";
        $svg .= '.info-text { font-family: Inter, Arial, sans-serif; font-size: 16px; fill: #374151; }' . "\n";
        $svg .= '.brand-area { fill: #dbeafe; stroke: #1e3a8a; stroke-width: 2; }' . "\n";
        $svg .= '</style>' . "\n";
        $svg .= '</defs>' . "\n";

        // Background
        $svg .= '<rect width="' . $width . '" height="' . $height . '" fill="white" stroke="#000" stroke-width="2"/>' . "\n";

        // Title
        $svg .= '<text x="' . ($width / 2) . '" y="40" text-anchor="middle" class="text-label">FOTOKU DESIGN TEMPLATE - ' . $slots . ' SLOTS</text>' . "\n";
        $svg .= '<text x="' . ($width / 2) . '" y="65" text-anchor="middle" class="info-text">A5 Size: ' . $width . ' × ' . $height . ' pixels @ 300 DPI</text>' . "\n";

        // Margin guides
        $svg .= '<rect x="' . $layout['margin'] . '" y="80" width="' . ($width - 2 * $layout['margin']) . '" height="' . ($height - $layout['bottom_area'] - 80 - $layout['margin']) . '" fill="none" stroke="#10b981" stroke-width="2" stroke-dasharray="5,5"/>' . "\n";

        // Calculate photo positions
        $photoAreaWidth = $width - (2 * $layout['margin']);
        $photoAreaHeight = $height - $layout['bottom_area'] - 80 - $layout['margin'];

        $startX = $layout['margin'];
        $startY = 80;

        $photoNumber = 1;

        for ($row = 0; $row < $layout['rows']; $row++) {
            for ($col = 0; $col < $layout['cols']; $col++) {
                $x = $startX + ($col * ($layout['photo_width'] + $layout['spacing']));
                $y = $startY + ($row * ($layout['photo_height'] + $layout['spacing']));

                // Photo area rectangle
                $svg .= '<rect x="' . $x . '" y="' . $y . '" width="' . $layout['photo_width'] . '" height="' . $layout['photo_height'] . '" class="photo-area"/>' . "\n";

                // Photo number
                $svg .= '<text x="' . ($x + $layout['photo_width'] / 2) . '" y="' . ($y + $layout['photo_height'] / 2) . '" text-anchor="middle" class="text-label">FOTO ' . $photoNumber . '</text>' . "\n";

                // Dimensions
                $svg .= '<text x="' . ($x + $layout['photo_width'] / 2) . '" y="' . ($y + $layout['photo_height'] / 2 + 30) . '" text-anchor="middle" class="measurement">' . $layout['photo_width'] . ' × ' . $layout['photo_height'] . ' px</text>' . "\n";

                $photoNumber++;
            }
        }

        // Brand/Logo area
        $brandY = $height - $layout['bottom_area'];
        $svg .= '<rect x="' . $layout['margin'] . '" y="' . $brandY . '" width="' . ($width - 2 * $layout['margin']) . '" height="' . ($layout['bottom_area'] - $layout['margin']) . '" class="brand-area"/>' . "\n";
        $svg .= '<text x="' . ($width / 2) . '" y="' . ($brandY + 40) . '" text-anchor="middle" class="text-label">AREA BRANDING</text>' . "\n";
        $svg .= '<text x="' . ($width / 2) . '" y="' . ($brandY + 65) . '" text-anchor="middle" class="info-text">Logo, Customer Info, QR Code dari Desain</text>' . "\n";

        // Measurement annotations
        $svg .= '<g class="measurements">' . "\n";

        // Canvas dimensions
        $svg .= '<text x="10" y="' . ($height / 2) . '" class="measurement" transform="rotate(-90 10 ' . ($height / 2) . ')">' . $height . ' px</text>' . "\n";
        $svg .= '<text x="' . ($width / 2) . '" y="' . ($height - 10) . '" text-anchor="middle" class="measurement">' . $width . ' px</text>' . "\n";

        // Margins
        $svg .= '<text x="' . ($layout['margin'] / 2) . '" y="150" text-anchor="middle" class="measurement" transform="rotate(-90 ' . ($layout['margin'] / 2) . ' 150)">Margin: ' . $layout['margin'] . 'px</text>' . "\n";

        // Spacing
        if ($layout['cols'] > 1) {
            $spacingX = $startX + $layout['photo_width'] + ($layout['spacing'] / 2);
            $svg .= '<text x="' . $spacingX . '" y="150" text-anchor="middle" class="measurement">Spacing: ' . $layout['spacing'] . 'px</text>' . "\n";
        }

        $svg .= '</g>' . "\n";

        // Instructions
        $instructionY = $height - 140;
        $svg .= '<text x="' . $layout['margin'] . '" y="' . $instructionY . '" class="info-text" font-weight="bold">PETUNJUK DESAIN:</text>' . "\n";
        $instructionY += 25;
        $svg .= '<text x="' . $layout['margin'] . '" y="' . $instructionY . '" class="info-text">• Area abu-abu adalah tempat penempatan foto</text>' . "\n";
        $instructionY += 20;
        $svg .= '<text x="' . $layout['margin'] . '" y="' . $instructionY . '" class="info-text">• Area biru adalah tempat logo dan branding</text>' . "\n";
        $instructionY += 20;
        $svg .= '<text x="' . $layout['margin'] . '" y="' . $instructionY . '" class="info-text">• Gunakan ukuran dan jarak yang sudah ditentukan</text>' . "\n";
        $instructionY += 20;
        $svg .= '<text x="' . $layout['margin'] . '" y="' . $instructionY . '" class="info-text">• Simpan dalam format PSD dengan layer terpisah</text>' . "\n";

        $svg .= '</svg>' . "\n";

        return $svg;
    }
}
