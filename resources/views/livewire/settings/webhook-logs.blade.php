<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 sm:px-0">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">System Events & Webhook Logs</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Pantau status pengiriman webhook Anda secara real-time.</p>
            </div>
            <a href="{{ route('settings.webhooks') }}" wire:navigate class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 0118 0z"></path></svg>
                Kembali ke Pengaturan
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-[2rem] border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-0">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/40">
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">Waktu</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-center">Event</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-center">Status</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-center">Upstream</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700 text-center">Attempt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($logs as $log)
                        <tr class="group hover:bg-gray-50/30 dark:hover:bg-gray-900/40 transition-all">
                            <td class="px-8 py-5">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $log->created_at->format('d M, H:i:s') }}</div>
                                <div class="text-[10px] text-gray-400 uppercase tracking-tighter">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-400 uppercase tracking-tighter border border-indigo-100/50 dark:border-indigo-800/30">
                                    {{ $log->event_type }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                @php
                                    $statusColor = match($log->status) {
                                        'success' => 'emerald',
                                        'pending' => 'amber',
                                        'failed' => 'rose',
                                        default => 'gray'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-black uppercase text-{{ $statusColor }}-600 dark:text-{{ $statusColor }}-400 bg-{{ $statusColor }}-50 dark:bg-{{ $statusColor }}-900/20 ring-1 ring-{{ $statusColor }}-100 dark:ring-{{ $statusColor }}-800">
                                    {{ $log->status }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center font-mono text-xs">
                                @if($log->response_code)
                                    <span class="text-{{ $log->response_code >= 400 ? 'rose' : 'emerald' }}-600 font-bold px-2 py-1 bg-gray-100 dark:bg-gray-900 rounded-lg">{{ $log->response_code }}</span>
                                @else
                                    <span class="text-gray-400 italic">No Response</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="text-sm font-bold text-gray-600 dark:text-gray-400">{{ $log->attempt }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center text-gray-400 italic">Belum ada aktivitas webhook.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
            <div class="px-8 py-6 bg-gray-50/50 dark:bg-gray-900/40 border-t border-gray-100 dark:border-gray-700">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
