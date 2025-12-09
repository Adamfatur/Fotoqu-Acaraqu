@extends('admin.layout')

@section('title', 'Detail Template Frame')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Template Frame</h1>
            <p class="text-gray-500">{{ $frame_template->name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.frame-templates.edit', $frame_template) }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('admin.frame-templates.index') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Template Info -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Info -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Template</label>
                        <p class="text-gray-900">{{ $frame_template->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <p class="text-gray-900">{{ $frame_template->description ?: 'Tidak ada deskripsi' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Slot</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ $frame_template->slots }} Slot
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            {{ $frame_template->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $frame_template->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    
                    @if($frame_template->is_default)
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-star mr-1"></i>Template Default
                        </span>
                    </div>
                    @endif
                </div>

                <!-- Technical Info -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dimensi</label>
                        <p class="text-gray-900">{{ $frame_template->width }} x {{ $frame_template->height }} px</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Warna Background</label>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 rounded border border-gray-300" 
                                 style="background-color: {{ $frame_template->background_color }}"></div>
                            <span class="text-gray-900">{{ $frame_template->background_color }}</span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dibuat</label>
                        <p class="text-gray-900">{{ $frame_template->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Terakhir Diupdate</label>
                        <p class="text-gray-900">{{ $frame_template->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Images Section -->
        @if($frame_template->template_path || $frame_template->preview_path)
        <div class="border-t border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Gambar Template</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($frame_template->template_path)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Template Image</label>
                    <img src="{{ $frame_template->template_url }}" 
                         alt="Template Image" 
                         class="rounded-lg border border-gray-200 max-h-64 w-auto">
                </div>
                @endif
                
                @if($frame_template->preview_path)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview Image</label>
                    <img src="{{ $frame_template->preview_url }}" 
                         alt="Preview Image" 
                         class="rounded-lg border border-gray-200 max-h-64 w-auto">
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Usage Stats -->
        <div class="border-t border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Penggunaan</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $frame_template->frames()->count() }}</div>
                    <div class="text-sm text-gray-500">Total Frame Dibuat</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $frame_template->frames()->whereDate('created_at', today())->count() }}</div>
                    <div class="text-sm text-gray-500">Frame Hari Ini</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $frame_template->frames()->whereDate('created_at', '>=', now()->subDays(7))->count() }}</div>
                    <div class="text-sm text-gray-500">Frame Minggu Ini</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
