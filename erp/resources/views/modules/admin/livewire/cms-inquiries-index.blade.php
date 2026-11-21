<div class="space-y-6 max-w-7xl mx-auto font-sans">
    <!-- Executive Header Card -->
    <div class="bg-gradient-to-r from-[#050C1A] via-[#08142C] to-[#0D1E3D] border border-white/10 rounded-2xl p-6 sm:p-8 text-white shadow-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-accent-500/20 text-accent-500 border border-accent-500/30 text-[11px] font-mono font-bold uppercase tracking-wider mb-2">
                <span class="w-2 h-2 rounded-full bg-accent-500"></span>
                <span>Client Inbound Project Briefs</span>
            </div>
            <h2 class="text-2xl font-heading font-black tracking-tight">
                {{ app()->getLocale() === 'ar' ? 'صندوق رسائل وطلبات المشاريع (Contact Inquiries)' : 'Client Contact Inquiries & Project Briefs' }}
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 mt-1">
                {{ app()->getLocale() === 'ar' ? 'استقبال ومراجعة طلبات المشاريع والاستشارات المرسلة عبر نموذج التواصل (/contact) والرد المباشر عليها.' : 'Inbound project briefs, RFPs, and partnership inquiries submitted via /contact.' }}
            </p>
        </div>

        <div class="flex items-center gap-2 text-xs font-mono shrink-0">
            <span class="px-3.5 py-2 bg-white/10 border border-white/15 rounded-xl text-white">Total: <strong>{{ $totalCount }}</strong></span>
            <span class="px-3.5 py-2 bg-accent-500 text-white rounded-xl font-bold shadow-glow">Unread: <strong>{{ $unreadCount }}</strong></span>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-subtle flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs font-mono">
        <div class="flex items-center gap-2">
            <button wire:click="$set('filter', 'all')" class="px-3.5 py-2 rounded-xl cursor-pointer {{ $filter === 'all' ? 'bg-ink-900 text-white font-bold' : 'bg-slate-100 text-slate-600' }}">All Messages</button>
            <button wire:click="$set('filter', 'unread')" class="px-3.5 py-2 rounded-xl cursor-pointer {{ $filter === 'unread' ? 'bg-accent-500 text-white font-bold' : 'bg-slate-100 text-slate-600' }}">Unread Only</button>
            <button wire:click="$set('filter', 'replied')" class="px-3.5 py-2 rounded-xl cursor-pointer {{ $filter === 'replied' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-600' }}">Replied</button>
        </div>

        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search sender, email, subject..." class="w-full sm:w-64 px-3.5 py-2 text-xs border border-slate-200 rounded-xl outline-none font-sans" />
    </div>

    <!-- Messages Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle overflow-hidden">
        <table class="w-full text-start text-xs font-sans">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50/70 text-slate-400 font-mono uppercase text-[10px]">
                    <th class="py-3.5 px-5 text-start">Sender Name</th>
                    <th class="py-3.5 px-4 text-start">Subject / Project Topic</th>
                    <th class="py-3.5 px-4 text-start">Received</th>
                    <th class="py-3.5 px-4 text-start">Status</th>
                    <th class="py-3.5 px-5 text-end">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($messages as $msg)
                    <tr class="hover:bg-slate-50/80 transition-colors {{ !$msg->is_read ? 'bg-accent-50/30 font-semibold' : '' }}">
                        <td class="py-4 px-5">
                            <div class="font-bold text-ink-900 text-sm flex items-center gap-2">
                                {{ $msg->name }}
                                @if(!$msg->is_read)
                                    <span class="w-2 h-2 rounded-full bg-accent-500"></span>
                                @endif
                            </div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $msg->email }} • {{ $msg->phone ?: 'No phone' }}</div>
                        </td>
                        <td class="py-4 px-4 max-w-sm">
                            <div class="font-bold text-ink-900 truncate">{{ $msg->subject ?: 'Project Inquiry' }}</div>
                            <div class="text-xs text-slate-500 truncate mt-0.5">{{ Str::limit($msg->message, 90) }}</div>
                        </td>
                        <td class="py-4 px-4 font-mono text-xs text-slate-500">
                            {{ $msg->created_at?->diffForHumans() }}
                        </td>
                        <td class="py-4 px-4 font-mono text-[10px]">
                            @if($msg->is_replied)
                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold">✓ REPLIED</span>
                            @elseif(!$msg->is_read)
                                <span class="px-2.5 py-1 rounded-full bg-accent-500 text-white font-bold">● NEW</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">READ</span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-end">
                            <div class="inline-flex items-center gap-2">
                                <button wire:click="view({{ $msg->id }})" class="px-3.5 py-1.5 text-ink-900 hover:text-white hover:bg-ink-900 font-bold border border-slate-200 rounded-xl bg-white transition-all cursor-pointer">
                                    Open Brief
                                </button>
                                <button wire:click="delete({{ $msg->id }})" onclick="return confirm('Delete message?') || event.stopImmediatePropagation()" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-xl cursor-pointer">
                                    ✕
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">No contact inquiries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-100">
            {{ $messages->links() }}
        </div>
    </div>

    <!-- Message Detail Modal -->
    @if($showDetailModal && $viewingMessage)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-950/75 backdrop-blur-xs">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-xl p-6 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-wider text-accent-500 font-bold">Client Project Brief</span>
                        <h3 class="text-lg font-heading font-extrabold text-ink-900">{{ $viewingMessage->name }}</h3>
                    </div>
                    <button wire:click="$set('showDetailModal', false)" class="text-slate-400 hover:text-slate-700">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100 font-mono">
                        <div>
                            <span class="text-slate-400 block text-[10px]">Email Address:</span>
                            <a href="mailto:{{ $viewingMessage->email }}" class="text-accent-500 font-bold hover:underline">{{ $viewingMessage->email }}</a>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Phone / WhatsApp:</span>
                            <span class="text-ink-900">{{ $viewingMessage->phone ?: 'N/A' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-slate-400 block text-[10px]">Subject:</span>
                            <strong class="text-ink-900">{{ $viewingMessage->subject ?: 'General Project Inquiry' }}</strong>
                        </div>
                    </div>

                    <div>
                        <span class="font-bold text-slate-700 block mb-1">Project Context & Message:</span>
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 leading-relaxed max-h-56 overflow-y-auto whitespace-pre-wrap">
                            {{ $viewingMessage->message }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <button wire:click="toggleReplied({{ $viewingMessage->id }})" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold cursor-pointer {{ $viewingMessage->is_replied ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-white hover:bg-slate-50 text-ink-900' }}">
                        {{ $viewingMessage->is_replied ? '✓ Marked Replied' : 'Mark as Replied' }}
                    </button>

                    <div class="flex items-center gap-2">
                        <a href="mailto:{{ $viewingMessage->email }}?subject=Re: {{ urlencode((string)$viewingMessage->subject) }}" target="_blank" class="px-5 py-2.5 bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow-glow">
                            Reply via Email ✉
                        </a>
                        <button wire:click="$set('showDetailModal', false)" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
