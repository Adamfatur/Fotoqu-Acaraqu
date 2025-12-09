{{-- Live Activities Component --}}
<div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-200">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Live Activities</h3>
            <p class="text-gray-600">Real-time sistem aktivitas</p>
        </div>
        <div class="flex items-center justify-between sm:justify-end space-x-4 w-full sm:w-auto">
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-teal-500 rounded-full animate-pulse"></div>
                <span class="text-xs text-teal-700 font-semibold">Live</span>
            </div>
            <div
                class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-teal-500 to-indigo-700 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-bolt text-indigo-100 text-sm sm:text-base"></i>
            </div>
        </div>
    </div>
    <div class="space-y-4 max-h-96 overflow-y-auto custom-scrollbar" id="activity-feed">
        @forelse($recentActivities as $activity)
            <div class="flex items-start space-x-3 p-4 hover:bg-gray-50 rounded-2xl transition-all duration-300 group">
                <div
                    class="w-8 h-8 bg-gradient-to-r from-indigo-700 to-teal-500 rounded-full flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <i class="fas fa-{{ $activity->icon ?? 'bolt' }} text-indigo-100 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 group-hover:text-indigo-800 transition-colors">
                        {{ $activity->description }}</p>
                    <div class="flex items-center mt-1 space-x-2">
                        @if($activity->user)
                            <span class="text-xs text-gray-600">oleh {{ $activity->user->name }}</span>
                        @endif
                        <span class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div
                    class="w-16 h-16 bg-gradient-to-r from-indigo-700 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-history text-indigo-100 text-2xl"></i>
                </div>
                <p class="text-gray-700 font-semibold">Belum ada aktivitas</p>
                <p class="text-gray-500 text-sm mt-1">Aktivitas sistem akan muncul di sini.</p>
            </div>
        @endforelse
    </div>
</div>