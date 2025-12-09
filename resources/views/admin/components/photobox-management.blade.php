{{-- Fotobooth Management Component --}}
<div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-200 mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Fotobooth Management</h3>
            <p class="text-gray-600">Real-time status dan kontrol Fotobooth</p>
        </div>
        <div class="flex items-center justify-between sm:justify-end space-x-4 w-full sm:w-auto">
            <button @click="refreshPhotoboxes()"
                class="p-3 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors"
                :disabled="loading.photoboxes">
                <i class="fas fa-sync-alt" :class="loading.photoboxes ? 'animate-spin' : ''"></i>
            </button>
            <div
                class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-indigo-700 to-teal-500 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-box text-indigo-100 text-sm sm:text-base"></i>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-4 sm:gap-6" id="photobox-grid">
        @forelse($activePhotoboxes as $photobox)
            <div class="p-6 border border-gray-200 rounded-2xl hover:shadow-lg transition-all duration-300 cursor-pointer bg-gray-50 hover:bg-white group"
                @click="showPhotoboxDetail('{{ $photobox->id }}')">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-indigo-700 to-teal-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                            <span class="text-gray-100 text-sm font-bold">{{ substr($photobox->code, -2) }}</span>
                        </div>
                        <div>
                            <span
                                class="font-semibold text-gray-800 group-hover:text-indigo-800 transition-colors">{{ $photobox->code }}</span>
                            <p class="text-xs text-gray-500">{{ $photobox->name }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col items-end space-y-1">
                        @php
                            $running = $photobox->activePhotoSessions->firstWhere('session_status', 'in_progress')
                                ?? $photobox->activePhotoSessions->firstWhere('session_status', 'photo_selection')
                                ?? $photobox->activePhotoSessions->firstWhere('session_status', 'processing');
                            $queued = $photobox->activePhotoSessions->where('session_status', 'approved')->values();
                            $queueCount = $queued->count();
                        @endphp
                        <div class="flex items-center space-x-2">
                            <div
                                class="w-2 h-2 {{ $running ? 'bg-amber-400' : ($queueCount > 0 ? 'bg-indigo-400' : 'bg-teal-500 animate-pulse') }} rounded-full">
                            </div>
                            <span
                                class="text-xs font-semibold {{ $running ? 'text-amber-600' : ($queueCount > 0 ? 'text-indigo-700' : 'text-teal-700') }}">
                                {{ $running ? 'In Use' : ($queueCount > 0 ? 'Queued' : 'Available') }}
                            </span>
                            @if($queueCount > 0)
                                <span
                                    class="ml-2 text-[10px] px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 font-bold">Antrian:
                                    {{ $queueCount }}</span>
                            @endif
                        </div>
                        @if($running)
                            <span class="text-xs text-gray-700 font-medium">Sedang: {{ $running->session_code }}</span>
                        @endif
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Lokasi:</span>
                        <span class="font-medium text-gray-800">{{ $photobox->location }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Sesi Hari Ini:</span>
                        <span class="font-semibold text-indigo-700">{{ $photobox->today_sessions_count }}</span>
                    </div>
                    @if($running)
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                <span>Progress Sesi</span>
                                <span>{{ $running->photos->count() }}/10</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-gradient-to-r from-indigo-500 to-teal-400 h-1.5 rounded-full transition-all"
                                    style="width: {{ min(($running->photos->count() / 10) * 100, 100) }}%"></div>
                            </div>
                        </div>
                    @elseif($queueCount > 0)
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span>Sesi berikutnya menunggu giliran</span>
                                <span class="text-indigo-700 font-semibold">Antrian: {{ $queueCount }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-gray-200 transition-opacity duration-300">
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-2">
                            @if($photobox->isAvailable())
                                <button @click.stop="startTestSession('{{ $photobox->id }}')"
                                    class="px-3 py-1 bg-teal-600 text-teal-50 rounded-lg text-xs hover:bg-teal-700 transition-colors font-medium">
                                    <i class="fas fa-play mr-1"></i>Test
                                </button>
                            @else
                                <div class="flex space-x-1">
                                    <button @click.stop="forceStop('{{ $photobox->id }}')"
                                        class="px-3 py-1 bg-rose-600 text-rose-50 rounded-lg text-xs hover:bg-rose-700 transition-colors font-medium">
                                        <i class="fas fa-stop mr-1"></i>Stop
                                    </button>

                                </div>
                            @endif
                        </div>
                        <span class="text-xs text-gray-500">
                            {{ $photobox->updated_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                @if($queueCount > 0)
                    <div class="mt-4">
                        <div class="text-xs text-gray-600 mb-2 font-semibold">Daftar Antrian (maks 3)</div>
                        <div class="space-y-1">
                            @foreach($queued->take(3) as $i => $qs)
                                <div
                                    class="flex items-center justify-between text-xs bg-indigo-50/60 border border-indigo-100 rounded-lg px-2 py-1">
                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-200 text-indigo-800 font-bold">{{ $i + 1 }}</span>
                                        <span class="font-medium text-gray-800">{{ $qs->session_code }}</span>
                                    </div>
                                    <div class="text-[11px] text-gray-600 truncate max-w-[50%]">{{ $qs->customer_name }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div
                    class="w-20 h-20 bg-gradient-to-r from-indigo-700 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-box text-indigo-100 text-3xl"></i>
                </div>
                <p class="text-gray-700 font-semibold">Tidak ada Fotobooth aktif</p>
                <p class="text-gray-500 text-sm mt-1">Setup Fotobooth untuk mulai menggunakan sistem.</p>
                <button
                    class="inline-flex items-center mt-4 px-6 py-3 bg-gradient-to-r from-indigo-700 to-indigo-500 text-indigo-50 rounded-xl hover:shadow-lg transition-all font-semibold">
                    <i class="fas fa-plus mr-2"></i>
                    Setup Fotobooth
                </button>
            </div>
        @endforelse
    </div>
</div>