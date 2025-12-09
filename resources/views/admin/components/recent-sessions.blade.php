{{-- Recent Sessions Component --}}
<div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-200">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Sesi Terbaru</h3>
            <p class="text-gray-600">Aktivitas sesi foto terkini</p>
        </div>
        <div class="flex items-center justify-between sm:justify-end space-x-4 w-full sm:w-auto">
            <button @click="refreshSessions()"
                class="p-3 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors"
                :disabled="loading.sessions">
                <i class="fas fa-sync-alt" :class="loading.sessions ? 'animate-spin' : ''"></i>
            </button>
            <a href="{{ route('admin.sessions.index') }}"
                class="text-sm font-semibold text-indigo-700 hover:text-indigo-800 transition-colors flex items-center">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    <div class="space-y-4 max-h-96 overflow-y-auto custom-scrollbar" id="recent-sessions">
        @forelse($recentSessions as $session)
            <div class="group p-4 bg-gray-50 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-teal-50 rounded-2xl border border-gray-200 hover:border-indigo-200 transition-all duration-300 cursor-pointer hover-lift"
                @click="showSessionDetail('{{ $session->id }}')">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-indigo-700 to-teal-500 rounded-full flex items-center justify-center text-indigo-50 font-bold shadow-lg">
                            {{ substr($session->customer_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 group-hover:text-indigo-800 transition-colors">
                                {{ $session->customer_name }}</p>
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                <span>{{ $session->session_code }}</span>
                                <span>•</span>
                                <span>{{ $session->frame_slots }} slot</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if($session->session_status === 'completed') bg-teal-100 text-teal-800
                            @elseif($session->session_status === 'in_progress') bg-sky-100 text-sky-800
                            @elseif($session->session_status === 'approved') bg-indigo-100 text-indigo-800
                            @else bg-amber-100 text-amber-800 @endif">
                            <i class="fas fa-circle text-xs mr-1"></i>
                            {{ ucfirst(str_replace('_', ' ', $session->session_status)) }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">{{ $session->created_at->diffForHumans() }}</p>
                        <p class="text-sm font-semibold text-indigo-700">Rp
                            {{ number_format($session->total_price, 0, ',', '.') }}</p>
                    </div>
                </div>
                @if($session->session_status === 'in_progress')
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                            <span>Progress</span>
                            <span>{{ $session->photos->count() }}/10 foto</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-indigo-500 to-teal-400 h-2 rounded-full transition-all duration-1000"
                                style="width: {{ min(($session->photos->count() / 10) * 100, 100) }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12">
                <div
                    class="w-20 h-20 bg-gradient-to-r from-indigo-700 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-camera text-indigo-100 text-3xl"></i>
                </div>
                <p class="text-gray-700 font-semibold">Belum ada sesi foto</p>
                <p class="text-gray-500 text-sm mt-1">Sesi foto baru akan muncul di sini.</p>
                <a href="{{ route('admin.sessions.create') }}"
                    class="inline-flex items-center mt-4 px-6 py-3 bg-gradient-to-r from-indigo-700 to-indigo-500 text-indigo-50 rounded-xl hover:shadow-lg transition-all font-semibold">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Sesi Baru
                </a>
            </div>
        @endforelse
    </div>
</div>