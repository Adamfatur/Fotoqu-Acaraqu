@extends('admin.layout')

@section('title', 'Edit Template Frame')

@section('content')
{{-- Menggunakan style yang sama persis dengan halaman "Buat Template" Anda --}}
<style>
    :root {
        --page-bg: #f4f7fa;
        --border-color: #e9eef5;
        --navy-color: #1e3a8a;
        --text-primary: #2c3e50;
    }
    .accordion-item { border: 1px solid var(--border-color); border-radius: 1rem; overflow: hidden; }
    .accordion-header { display: flex; justify-content: space-between; align-items: center; width: 100%; padding: 1.25rem; background-color: #ffffff; cursor: pointer; font-size: 1.125rem; font-weight: 600; color: var(--text-primary); border-bottom: 1px solid transparent; }
    .accordion-item.open .accordion-header { border-bottom-color: var(--border-color); }
    .accordion-content { padding: 1.5rem; background-color: #fcfdff; }
    .accordion-icon { transition: transform 0.3s ease; }
    .accordion-item.open .accordion-icon { transform: rotate(180deg); }
    .canvas-sticky-container { position: sticky; top: 2rem; max-height: calc(100vh - 4rem); overflow-y: auto; }
    .canvas-card { background: #ffffff; border-radius: 1rem; border: 1px solid var(--border-color); box-shadow: 0 10px 30px -5px rgba(30, 58, 138, 0.05); }
    .canvas-header { padding: 1.25rem; border-bottom: 1px solid var(--border-color); background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 1rem 1rem 0 0; }
    .canvas-body { padding: 1.5rem; }
    @media (max-width: 1024px) {
        .canvas-sticky-container { position: relative; top: auto; max-height: none; }
    }
    .file-upload-box { border: 2px dashed var(--border-color); border-radius: 0.75rem; padding: 1.5rem; text-align: center; transition: all 0.3s ease; background: #fafbfc; min-height: 120px; display: flex; align-items: center; justify-content: center; }
    .file-upload-box:hover { border-color: var(--navy-color); background: #f0f4ff; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(30, 58, 138, 0.1); }
    .file-upload-box.has-file { border-color: #10b981; background: #f0fdf4; }
    .upload-content { width: 100%; }
    .upload-icon { font-size: 2.5rem; margin-bottom: 0.75rem; transition: all 0.3s ease; display: block; }
    .file-upload-box:hover .upload-icon { transform: scale(1.1); color: var(--navy-color) !important; }
    .file-upload-box.has-file .upload-icon { color: #10b981 !important; }
    .upload-text { font-weight: 600; margin-bottom: 0.25rem; color: #374151; }
    .upload-subtext { font-size: 0.875rem; color: #6b7280; line-height: 1.4; }
    .upload-filename { color: #10b981; font-weight: 600; word-break: break-all; margin-bottom: 0.25rem; font-size: 0.9rem; }
    .json-instructions { background-color: #f0f5ff; border: 1px solid #d6e4ff; border-radius: 0.75rem; padding: 1rem; font-size: 0.875rem; color: #434d5b; }
    .json-instructions ul { list-style-type: disc; padding-left: 1.25rem; margin-top: 0.5rem; }
    .json-instructions code { background-color: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-weight: 600; }
    .danger-zone { border: 2px solid #fee2e2; background-color: #fef2f2; border-radius: 1rem; overflow: hidden; }
</style>

<div class="max-w-7xl mx-auto space-y-6" x-data="frameTemplateBuilder()">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Template Frame</h1>
            <p class="text-gray-500">Memperbarui template: <span class="font-semibold">{{ $frame_template->name }}</span></p>
        </div>
        <a href="{{ route('admin.frame-templates.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white text-gray-700 rounded-lg font-semibold border border-gray-200 hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <form action="{{ route('admin.frame-templates.update', $frame_template) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            
            <div class="space-y-4" x-data="{ openSection: 'basic' }">
                <div class="accordion-item" :class="{'open': openSection === 'basic'}">
                    <button type="button" @click="openSection = (openSection === 'basic' ? '' : 'basic')" class="accordion-header">
                        <span><i class="fas fa-info-circle mr-3 text-blue-500"></i>Informasi Dasar</span>
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div x-show="openSection === 'basic'" x-collapse class="accordion-content">
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Template <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name', $frame_template->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('name') border-red-500 @enderror" required>
                                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="slots" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Slot <span class="text-red-500">*</span></label>
                                <select id="slots" name="slots" x-model="formData.slots" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('slots') border-red-500 @enderror" required>
                                    <option value="">Pilih Jumlah Slot</option>
                                    <option value="2" @if(old('slots', $frame_template->slots) == '2') selected @endif>2 Foto (4R Utuh)</option>
                                    <option value="4" @if(old('slots', $frame_template->slots) == '4') selected @endif>4 Foto (4R Utuh)</option>
                                    <option value="6" @if(old('slots', $frame_template->slots) == '6') selected @endif>6 Foto (4R dibagi 2 Strip 2R)</option>
                                </select>
                                @error('slots')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                                <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('description') border-red-500 @enderror">{{ old('description', $frame_template->description) }}</textarea>
                                @error('description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item" :class="{'open': openSection === 'design'}">
                    <button type="button" @click="openSection = (openSection === 'design' ? '' : 'design')" class="accordion-header">
                         <span><i class="fas fa-paint-brush mr-3 text-emerald-500"></i>Pengaturan Desain</span>
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div x-show="openSection === 'design'" x-collapse class="accordion-content">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                             <div>
                                <label for="width" class="block text-sm font-medium text-gray-700 mb-2">Lebar (px) <span class="text-red-500">*</span></label>
                                <input type="number" id="width" name="width" value="{{ old('width', $frame_template->width) }}" x-model.number="formData.width" min="500" max="5000" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('width') border-red-500 @enderror" required>
                            </div>
                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700 mb-2">Tinggi (px) <span class="text-red-500">*</span></label>
                                <input type="number" id="height" name="height" value="{{ old('height', $frame_template->height) }}" x-model.number="formData.height" min="500" max="5000" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('height') border-red-500 @enderror" required>
                            </div>
                             <div class="sm:col-span-2">
                                <label for="background_color" class="block text-sm font-medium text-gray-700 mb-2">Warna Latar <span class="text-red-500">*</span></label>
                                <div class="flex items-center gap-3">
                                    <input type="color" id="background_color" name="background_color" value="{{ old('background_color', $frame_template->background_color) }}" x-model="formData.backgroundColor" class="h-11 w-12 border border-gray-300 rounded-lg cursor-pointer p-1">
                                    <input type="text" x-model="formData.backgroundColor" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg @error('background_color') border-red-500 @enderror" pattern="^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item" :class="{'open': openSection === 'files'}">
                     <button type="button" @click="openSection = (openSection === 'files' ? '' : 'files')" class="accordion-header">
                        <span><i class="fas fa-upload mr-3 text-orange-500"></i>File & Opsi</span>
                        <i class="fas fa-chevron-down accordion-icon"></i>
                    </button>
                    <div x-show="openSection === 'files'" x-collapse class="accordion-content">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Template Image <span class="text-red-500">*</span></label>
                                <label for="template_image" class="file-upload-box cursor-pointer" :class="{'has-file': files.template}"><input type="file" id="template_image" name="template_image" class="hidden" @change="handleFileUpload($event, 'template')"><div class="upload-content"><div class="upload-icon text-gray-400" x-show="!files.template"><i class="fas fa-cloud-upload-alt"></i></div><div class="upload-icon text-green-500" x-show="files.template"><i class="fas fa-check-circle"></i></div><div x-show="!files.template"><p class="upload-text text-gray-700">Ganti Template Image</p><p class="upload-subtext">Klik atau drag & drop file baru</p></div><div x-show="files.template"><p class="upload-filename" x-text="files.template?.name"></p><p class="upload-subtext">File baru dipilih • <button type="button" @click.stop="removeFile('template')" class="text-red-500 hover:text-red-700 underline">Hapus</button></p></div></div></label>
                                @error('template_image')<p class="text-red-500 text-sm mt-2">{{ $message }}</p>@enderror
                                @if($frame_template->template_image_url)
                                <div class="mt-4"><p class="text-xs font-semibold text-gray-600 mb-2">GAMBAR SAAT INI:</p><img src="{{ $frame_template->template_image_url }}" alt="Current Template Image" class="rounded-lg border border-gray-200 max-h-40"></div>
                                @endif
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Preview Image <span class="text-gray-400">(Opsional)</span></label>
                                <label for="preview_image" class="file-upload-box cursor-pointer" :class="{'has-file': files.preview}"><input type="file" id="preview_image" name="preview_image" class="hidden" @change="handleFileUpload($event, 'preview')"><div class="upload-content"><div class="upload-icon text-gray-400" x-show="!files.preview"><i class="fas fa-image"></i></div><div class="upload-icon text-green-500" x-show="files.preview"><i class="fas fa-check-circle"></i></div><div x-show="!files.preview"><p class="upload-text text-gray-700">Ganti Preview Image</p><p class="upload-subtext">Klik atau drag & drop file baru</p></div><div x-show="files.preview"><p class="upload-filename" x-text="files.preview?.name"></p><p class="upload-subtext">File baru dipilih • <button type="button" @click.stop="removeFile('preview')" class="text-red-500 hover:text-red-700 underline">Hapus</button></p></div></div></label>
                                @error('preview_image')<p class="text-red-500 text-sm mt-2">{{ $message }}</p>@enderror
                                @if($frame_template->preview_url)
                                <div class="mt-4"><p class="text-xs font-semibold text-gray-600 mb-2">PREVIEW SAAT INI:</p><img src="{{ $frame_template->preview_url }}" alt="Current Preview Image" class="rounded-lg border border-gray-200 max-h-40"></div>
                                @endif
                            </div>
                            <div class="border-t pt-4 space-y-3">
                                <label class="flex items-start cursor-pointer group"><input type="checkbox" id="is_default" name="is_default" value="1" @if(old('is_default', $frame_template->is_default)) checked @endif class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 mt-1"><div class="ml-3"><span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Jadikan template default</span><p class="text-xs text-gray-500 mt-1">Template ini akan menjadi pilihan utama untuk {{ $frame_template->slots }} slot</p></div></label>
                                <label class="flex items-start cursor-pointer group"><input type="checkbox" id="is_recommended" name="is_recommended" value="1" @if(old('is_recommended', $frame_template->is_recommended)) checked @endif class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 mt-1"><div class="ml-3"><span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Tandai sebagai Recommended</span><p class="text-xs text-gray-500 mt-1">Ditampilkan menonjol di daftar admin dan UI photobox</p></div></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="danger-zone">
                     <button type="button" @click="confirmingDelete = true" class="w-full text-left p-4">
                        <h3 class="font-bold text-red-700">Hapus Template Ini</h3>
                        <p class="text-sm text-red-500 mt-1">Tindakan ini tidak dapat dibatalkan.</p>
                     </button>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4">
                    <a href="{{ route('admin.frame-templates.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold">Batal</a>
                    <button type="submit" class="px-8 py-2.5 bg-navy-600 text-white rounded-lg hover:bg-navy-700 font-semibold shadow-lg" style="background-color: var(--navy-color)"><i class="fas fa-save mr-2"></i>Simpan Perubahan</button>
                </div>
            </div>

            <div class="canvas-sticky-container">
                <div class="canvas-card">
                    <div class="canvas-header"><h3 class="font-semibold text-lg"><i class="fas fa-drafting-compass mr-2"></i>Kanvas & Layout</h3></div>
                    <div class="canvas-body">
                        <div class="bg-gray-100 rounded-lg p-4 mb-4 border" :style="`background-color: ${formData.backgroundColor}`">
                             <div x-show="!formData.slots" class="text-center py-16 text-gray-500"><i class="fas fa-th-large text-3xl mb-2"></i><p>Pilih jumlah slot</p></div>
                             
                             <!-- 6 Slots: 4x6 Fotostrip Layout -->
                             <div x-show="formData.slots == '6'" class="grid grid-cols-2 gap-3 max-w-lg mx-auto">
                                <!-- Left Strip -->
                                <div class="bg-white/80 rounded-lg p-3 border">
                                    <div class="mb-2 h-8 bg-green-50/70 border-2 border-dashed border-green-300 rounded-lg flex items-center justify-center">
                                        <span class="text-green-600 text-xs">Logo/Brand</span>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="aspect-[3/2] bg-white/70 border-2 border-dashed border-gray-400 rounded-lg flex items-center justify-center"><span class="text-gray-500 text-xs">#1</span></div>
                                        <div class="aspect-[3/2] bg-white/70 border-2 border-dashed border-gray-400 rounded-lg flex items-center justify-center"><span class="text-gray-500 text-xs">#2</span></div>
                                        <div class="aspect-[3/2] bg-white/70 border-2 border-dashed border-gray-400 rounded-lg flex items-center justify-center"><span class="text-gray-500 text-xs">#3</span></div>
                                    </div>
                                </div>
                                <!-- Right Strip (Duplicates) -->
                                <div class="bg-blue-50/80 rounded-lg p-3 border">
                                    <div class="mb-2 h-8 bg-blue-100/70 border-2 border-dashed border-blue-300 rounded-lg flex items-center justify-center">
                                        <span class="text-blue-600 text-xs">Logo/Brand</span>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="aspect-[3/2] bg-blue-50/70 border-2 border-dashed border-blue-300 rounded-lg flex items-center justify-center"><span class="text-blue-500 text-xs">Dup #1</span></div>
                                        <div class="aspect-[3/2] bg-blue-50/70 border-2 border-dashed border-blue-300 rounded-lg flex items-center justify-center"><span class="text-blue-500 text-xs">Dup #2</span></div>
                                        <div class="aspect-[3/2] bg-blue-50/70 border-2 border-dashed border-blue-300 rounded-lg flex items-center justify-center"><span class="text-blue-500 text-xs">Dup #3</span></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 2 Slots: Single 4R Layout -->
                            <div x-show="formData.slots == '2'" class="flex flex-col gap-3 max-w-sm mx-auto p-4 bg-white border rounded-lg">
                                <div class="h-12 bg-purple-50 border-2 border-dashed border-purple-200 rounded-lg flex items-center justify-center mb-2">
                                    <span class="text-purple-500 text-xs font-semibold">Area Logo / Brand (Atas)</span>
                                </div>
                                <template x-for="i in 2">
                                     <div class="aspect-[3/2] bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                                        <span class="text-gray-400 font-medium" x-text="`Foto #${i}`"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- 4 Slots: Grid 2x2 4R Layout -->
                            <div x-show="formData.slots == '4'" class="max-w-sm mx-auto p-4 bg-white border rounded-lg">
                                <div class="h-12 bg-purple-50 border-2 border-dashed border-purple-200 rounded-lg flex items-center justify-center mb-4">
                                    <span class="text-purple-500 text-xs font-semibold">Area Logo / Brand (Atas)</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <template x-for="i in 4">
                                         <div class="aspect-[3/4] bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                                            <span class="text-gray-400 font-bold" x-text="`#${i}`"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div><h4 class="text-sm font-medium text-gray-700 mb-2">Preset Cepat:</h4><div class="flex flex-wrap gap-2"><button type="button" @click="applyPreset('grid')" class="px-3 py-1 text-xs bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200">Grid Standar</button><button type="button" @click="applyPreset('collage')" class="px-3 py-1 text-xs bg-purple-100 text-purple-600 rounded-full hover:bg-purple-200">Collage</button><button type="button" @click="applyPreset('strip')" class="px-3 py-1 text-xs bg-emerald-100 text-emerald-600 rounded-full hover:bg-emerald-200">Strip Vertikal</button></div></div>
                        <div class="mt-4">
                            <div class="flex items-center justify-between mb-2">
                                <label for="layout_config" class="block text-sm font-medium text-gray-700">Konfigurasi Layout (JSON)</label>
                                <button type="button" @click="setFixedDefaultLayout()" class="px-3 py-1.5 text-xs bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Gunakan Layout Default
                                </button>
                            </div>
                            <textarea id="layout_config" name="layout_config" rows="8" x-model="layoutConfigJSON" class="w-full px-4 py-3 border border-gray-300 rounded-lg font-mono text-xs @error('layout_config') border-red-500 @enderror" required>{{ old('layout_config') }}</textarea>
                            @error('layout_config')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1" x-show="lockLayout">Layout default diterapkan. Perubahan ukuran tidak akan mengubah JSON otomatis.</p>
                        </div>
                        <div class="mt-4 json-instructions"><h4 class="font-semibold text-blue-900 flex items-center"><i class="fas fa-info-circle mr-2"></i>Petunjuk Konfigurasi</h4><ul class="space-y-1 mt-2"><li><code>"x"</code>: Posisi horizontal (jarak dari kiri) dalam pixel.</li><li><code>"y"</code>: Posisi vertikal (jarak dari atas) dalam pixel.</li><li><code>"width"</code>: Lebar slot foto dalam pixel.</li><li><code>"height"</code>: Tinggi slot foto dalam pixel.</li></ul><p class="mt-2">Titik (0,0) adalah pojok kiri atas kanvas.</p></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <div x-show="confirmingDelete" @click.self="confirmingDelete = false" x-cloak class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 transition-opacity" :class="{'opacity-100': confirmingDelete, 'opacity-0': !confirmingDelete}">
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 transform transition-transform" :class="{'scale-100': confirmingDelete, 'scale-95': !confirmingDelete}">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full mx-auto mb-4 flex items-center justify-center"><i class="fas fa-trash text-2xl text-red-600"></i></div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Hapus Template?</h3>
                <p class="text-gray-600 mb-6">Anda yakin ingin menghapus template <strong class="font-semibold">"{{ $frame_template->name }}"</strong>? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex space-x-4">
                    <button @click="confirmingDelete = false" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-semibold transition-all">Batal</button>
                    <form action="{{ route('admin.frame-templates.destroy', $frame_template) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 font-semibold transition-all">Ya, Hapus Permanen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Menggunakan kembali AlpineJS Component yang sama, namun diinisialisasi dengan data $frame_template
function frameTemplateBuilder() {
    return {
        // --- Inisialisasi State dengan data dari $frame_template ---
        formData: {
            slots: '{{ old('slots', $frame_template->slots) }}',
            width: {{ old('width', $frame_template->width) }},
            height: {{ old('height', $frame_template->height) }},
            backgroundColor: '{{ old('background_color', $frame_template->background_color) }}'
        },
        files: { template: null, preview: null },
        // Handle layout_config - could be array (from model) or string (from old input)
        @php
            $layoutConfig = old('layout_config', $frame_template->layout_config);
            if (is_string($layoutConfig)) {
                $layoutConfig = json_decode($layoutConfig, true);
            }
        @endphp
        layoutConfigJSON: `{!! addslashes(json_encode($layoutConfig, JSON_PRETTY_PRINT)) !!}`,
        
        confirmingDelete: false, // State untuk modal hapus

        // --- Semua fungsi lainnya sama persis dengan halaman "Buat Template" ---
        init() {
            if (this.formData.slots && !this.lockLayout) {
                this.generateDefaultLayout();
            }
            this.$watch('formData', () => { if (!this.lockLayout) this.generateDefaultLayout(); });
        },
        generateDefaultLayout() {
            if (!this.formData.slots) {
                this.layoutConfigJSON = JSON.stringify({ "info": "Pilih jumlah slot untuk generate layout." }, null, 2);
                return;
            }
            if (this.lockLayout) return; // do not overwrite fixed default
            
            const slots = parseInt(this.formData.slots);
            let config = { type: 'custom', slots: [] };

            // 6 Slots: 4x6 Fotostrip (Existing Logic)
            if (slots === 6) {
                config = {
                    type: 'fotostrip',
                    format: '4x6_inch',
                    strips: 2,
                    photos_per_strip: 3,
                    slots: []
                };
                
                const logoHeight = 150;
                const logoMargin = 20;
                const photoWidth = 450;
                const photoHeight = 300;
                const photoSpacing = 20;
                const sideMargin = 60;
                const stripSpacing = 60;
                const topMargin = 50;
                
                const leftStripX = sideMargin;
                const rightStripX = leftStripX + photoWidth + stripSpacing;
                const logoY = topMargin;
                const photosStartY = logoY + logoHeight + logoMargin;
                
                // Logos
                config.slots.push({ x: leftStripX, y: logoY, width: photoWidth, height: logoHeight, strip: 0, position_in_strip: 'logo', is_logo: true, type: 'logo' });
                config.slots.push({ x: rightStripX, y: logoY, width: photoWidth, height: logoHeight, strip: 1, position_in_strip: 'logo', is_logo: true, type: 'logo' });
                
                // Photos
                for (let i = 0; i < 6; i++) {
                    const stripIndex = i < 3 ? 0 : 1;
                    const photoInStrip = i % 3;
                    const x = stripIndex === 0 ? leftStripX : rightStripX;
                    const y = photosStartY + (photoInStrip * (photoHeight + photoSpacing));
                    
                    config.slots.push({
                        x: x, y: y, width: photoWidth, height: photoHeight,
                        strip: stripIndex, position_in_strip: photoInStrip,
                        is_duplicate: stripIndex === 1
                    });
                }
            } 
            // 2 Slots: 4R Portrait Stacked (2 large photos) with Logo Space
            else if (slots === 2) {
                config.type = '2_photo_portrait';
                
                const canvasW = 1181;
                const canvasH = 1748;
                
                const logoH = 200; // Space for Logo
                const logoMargin = 50;
                const bottomMargin = 80;
                const sideMargin = 80;
                const photoGap = 40;
                
                // Add Logo Slot Marker for reference
                config.slots.push({ 
                    x: sideMargin, 
                    y: 50, 
                    width: canvasW - (sideMargin*2), 
                    height: logoH, 
                    type: 'logo', 
                    is_logo: true 
                });
                
                const startY = 50 + logoH + logoMargin;
                const availableH = canvasH - startY - bottomMargin;
                
                const photoH = Math.floor((availableH - photoGap) / 2);
                const photoW = canvasW - (sideMargin * 2);
                
                config.slots.push({ x: sideMargin, y: startY, width: photoW, height: photoH, position: 0 });
                config.slots.push({ x: sideMargin, y: startY + photoH + photoGap, width: photoW, height: photoH, position: 1 });
            }
            // 4 Slots: 4R Grid 2x2 with Logo Space
            else if (slots === 4) {
                 config.type = '4_photo_grid';
                 
                const canvasW = 1181;
                const canvasH = 1748;
                
                const logoH = 200; // Space for Logo
                const logoMargin = 40;
                const bottomMargin = 60;
                const sideMargin = 60;
                const gap = 40;

                // Add Logo Slot Marker
                config.slots.push({ 
                    x: sideMargin, 
                    y: 50, 
                    width: canvasW - (sideMargin*2), 
                    height: logoH, 
                    type: 'logo', 
                    is_logo: true 
                });
                 
                 const startY = 50 + logoH + logoMargin;
                 const availableW = canvasW - (2 * sideMargin) - gap;
                 const availableH = canvasH - startY - bottomMargin - gap;

                 const photoW = Math.floor(availableW / 2);
                 const photoH = Math.floor(availableH / 2);
                 
                 // Top Left
                 config.slots.push({ x: sideMargin, y: startY, width: photoW, height: photoH, position: 0 });
                 // Top Right
                 config.slots.push({ x: sideMargin + photoW + gap, y: startY, width: photoW, height: photoH, position: 1 });
                 // Bottom Left
                 config.slots.push({ x: sideMargin, y: startY + photoH + gap, width: photoW, height: photoH, position: 2 });
                 // Bottom Right
                 config.slots.push({ x: sideMargin + photoW + gap, y: startY + photoH + gap, width: photoW, height: photoH, position: 3 });
            } else {
                 // Fallback to basic grid for other numbers (like 8) if ever supported
                let cols = 2;
                let rows = Math.ceil(slots / 2);
                const gutter = 30; 
                const totalGutterX = gutter * (cols + 1); 
                const totalGutterY = gutter * (rows + 1);
                const slotWidth = Math.floor((this.formData.width - totalGutterX) / cols); 
                const slotHeight = Math.floor((this.formData.height - totalGutterY) / rows);
                
                for (let i = 0; i < slots; i++) {
                    const col = i % cols; 
                    const row = Math.floor(i / cols);
                    config.slots.push({ x: (gutter * (col + 1)) + (col * slotWidth), y: (gutter * (row + 1)) + (row * slotHeight), width: slotWidth, height: slotHeight });
                }
            }
            
            this.layoutConfigJSON = JSON.stringify(config, null, 2);
        },
        applyPreset(preset) {
            if (!this.formData.slots) { alert('Pilih jumlah slot terlebih dahulu.'); return; }
            this.generateDefaultLayout();
            alert(`Preset '${preset}' diterapkan dengan layout grid standar.`);
        },
        handleFileUpload(event, type) { const file = event.target.files[0]; if (file) { this.validateAndSetFile(file, type, event); } },
        handleFileDrop(event, type) { const files = event.dataTransfer.files; if (files.length > 0) { this.validateAndSetFile(files[0], type); } },
        validateAndSetFile(file, type, event = null) {
            if (file.size > 10 * 1024 * 1024) { alert('File terlalu besar! Maksimal 10MB.'); if (event) event.target.value = ''; return; }
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) { alert('Format file tidak didukung! Gunakan PNG, JPG, atau JPEG.'); if (event) event.target.value = ''; return; }
            this.files[type] = file;
        },
        removeFile(type) {
            this.files[type] = null;
            const input = document.getElementById(type === 'template' ? 'template_image' : 'preview_image');
            if (input) input.value = '';
        }
        ,
        lockLayout: false,
        setFixedDefaultLayout() {
            const fixed = { 
                type: 'grid',
                slots: [
                    { x: 30, y: 30, width: 555, height: 560 },
                    { x: 615, y: 30, width: 555, height: 560 },
                    { x: 30, y: 620, width: 555, height: 560 },
                    { x: 615, y: 620, width: 555, height: 560 },
                    { x: 30, y: 1210, width: 555, height: 560 },
                    { x: 615, y: 1210, width: 555, height: 560 }
                ]
            };
            this.layoutConfigJSON = JSON.stringify(fixed, null, 2);
            this.lockLayout = true; // prevent auto-regeneration from overwriting
            // Ensure slots set to 6 without triggering overwrite
            this.formData.slots = '6';
        }
    }
}
</script>
@endsection