<div class="space-y-6 max-w-7xl mx-auto font-sans">
    <!-- Executive Header Card -->
    <div class="bg-gradient-to-r from-[#050C1A] via-[#08142C] to-[#0D1E3D] border border-white/10 rounded-2xl p-6 sm:p-8 text-white shadow-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-accent-500/20 text-accent-500 border border-accent-500/30 text-[11px] font-mono font-bold uppercase tracking-wider mb-2">
                <span class="w-2 h-2 rounded-full bg-accent-500"></span>
                <span>Candidate Recruitment Pipeline</span>
            </div>
            <h2 class="text-2xl font-heading font-black tracking-tight">
                {{ app()->getLocale() === 'ar' ? 'صندوق طلبات التوظيف والسير الذاتية (Job Applications & CVs)' : 'Candidate Job Applications & Resumes' }}
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 mt-1">
                {{ app()->getLocale() === 'ar' ? 'مراجعة السير الذاتية وروابط الأعمال المقدمة من المرشحين عبر صفحات الوظائف وتحديد مرحلة التوظيف.' : 'Review applicant CVs, portfolios, and cover notes submitted via /careers/{slug}.' }}
            </p>
        </div>

        <div class="flex items-center gap-2 text-xs font-mono shrink-0">
            <span class="px-3.5 py-2 bg-white/10 border border-white/15 rounded-xl text-white">Total: <strong>{{ $totalApplications }}</strong></span>
            <span class="px-3.5 py-2 bg-accent-500 text-white rounded-xl font-bold shadow-glow">New: <strong>{{ $newCount }}</strong></span>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-subtle flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs font-mono">
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="$set('statusFilter', 'all')" class="px-3.5 py-2 rounded-xl cursor-pointer {{ $statusFilter === 'all' ? 'bg-ink-900 text-white font-bold' : 'bg-slate-100 text-slate-600' }}">All</button>
            <button wire:click="$set('statusFilter', 'new')" class="px-3.5 py-2 rounded-xl cursor-pointer {{ $statusFilter === 'new' ? 'bg-accent-500 text-white font-bold' : 'bg-slate-100 text-slate-600' }}">New</button>
            <button wire:click="$set('statusFilter', 'reviewing')" class="px-3.5 py-2 rounded-xl cursor-pointer {{ $statusFilter === 'reviewing' ? 'bg-ink-900 text-white font-bold' : 'bg-slate-100 text-slate-600' }}">Reviewing</button>
            <button wire:click="$set('statusFilter', 'shortlisted')" class="px-3.5 py-2 rounded-xl cursor-pointer {{ $statusFilter === 'shortlisted' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-600' }}">Shortlisted</button>
            <button wire:click="$set('statusFilter', 'rejected')" class="px-3.5 py-2 rounded-xl cursor-pointer {{ $statusFilter === 'rejected' ? 'bg-rose-600 text-white font-bold' : 'bg-slate-100 text-slate-600' }}">Rejected</button>
        </div>

        <div class="flex items-center gap-2">
            <select wire:model.live="jobFilter" class="px-3.5 py-2 text-xs border border-slate-200 rounded-xl bg-white font-mono">
                <option value="all">All Roles...</option>
                @foreach($jobs as $j)
                    <option value="{{ $j->id }}">{{ $j->title }}</option>
                @endforeach
            </select>

            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search candidate..." class="w-48 px-3.5 py-2 text-xs border border-slate-200 rounded-xl outline-none font-sans" />
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle overflow-hidden">
        <table class="w-full text-start text-xs font-sans">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50/70 text-slate-400 font-mono uppercase text-[10px]">
                    <th class="py-3.5 px-5 text-start">Candidate</th>
                    <th class="py-3.5 px-4 text-start">Applied Role</th>
                    <th class="py-3.5 px-4 text-start">Date</th>
                    <th class="py-3.5 px-4 text-start">Portfolio / Resume</th>
                    <th class="py-3.5 px-4 text-start">Stage</th>
                    <th class="py-3.5 px-5 text-end">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($applications as $app)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-5">
                            <div class="font-bold text-ink-900 text-sm">{{ $app->candidate_name }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $app->candidate_email }} • {{ $app->candidate_phone ?: 'No phone' }}</div>
                        </td>
                        <td class="py-4 px-4 font-mono text-xs text-ink-900 font-bold">
                            {{ $app->jobPosting?->title ?? 'General Application' }}
                        </td>
                        <td class="py-4 px-4 font-mono text-xs text-slate-500">
                            {{ $app->created_at?->format('Y-m-d') }}
                        </td>
                        <td class="py-4 px-4 font-mono text-xs">
                            <div class="flex items-center gap-2">
                                @if($app->resume_path)
                                    <a href="{{ asset($app->resume_path) }}" target="_blank" class="px-2.5 py-1 bg-slate-100 hover:bg-ink-900 hover:text-white rounded-lg border border-slate-200 font-bold">
                                        CV 📄
                                    </a>
                                @endif
                                @if($app->portfolio_url)
                                    <a href="{{ $app->portfolio_url }}" target="_blank" class="px-2.5 py-1 bg-slate-100 hover:bg-ink-900 hover:text-white rounded-lg border border-slate-200 font-bold">
                                        Portfolio ↗
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-2.5 py-1 rounded-full font-mono text-[10px] font-bold uppercase
                                {{ $app->status === 'new' ? 'bg-accent-50 text-accent-600 border border-accent-200' : ($app->status === 'shortlisted' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($app->status === 'rejected' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-700')) }}">
                                {{ $app->status }}
                            </span>
                        </td>
                        <td class="py-4 px-5 text-end">
                            <div class="inline-flex items-center gap-2">
                                <button wire:click="view({{ $app->id }})" class="px-3.5 py-1.5 text-ink-900 hover:text-white hover:bg-ink-900 font-bold border border-slate-200 rounded-xl bg-white transition-all cursor-pointer">
                                    Review
                                </button>
                                <button wire:click="delete({{ $app->id }})" onclick="return confirm('Delete applicant?') || event.stopImmediatePropagation()" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-xl cursor-pointer">
                                    ✕
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">No job applications submitted yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-100">
            {{ $applications->links() }}
        </div>
    </div>

    <!-- Candidate Review Detail Modal -->
    @if($showDetailModal && $viewingApplication)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-950/75 backdrop-blur-xs">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-xl p-6 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-wider text-accent-500 font-bold">Candidate Dossier</span>
                        <h3 class="text-lg font-heading font-extrabold text-ink-900">{{ $viewingApplication->candidate_name }}</h3>
                    </div>
                    <button wire:click="$set('showDetailModal', false)" class="text-slate-400 hover:text-slate-700">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100 font-mono">
                        <div>
                            <span class="text-slate-400 block text-[10px]">Role Applied:</span>
                            <strong class="text-ink-900">{{ $viewingApplication->jobPosting?->title ?? 'General' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Applied On:</span>
                            <strong class="text-ink-900">{{ $viewingApplication->created_at?->format('Y-m-d H:i') }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Email Address:</span>
                            <a href="mailto:{{ $viewingApplication->candidate_email }}" class="text-accent-500 font-bold hover:underline">{{ $viewingApplication->candidate_email }}</a>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Phone Number:</span>
                            <span class="text-ink-900">{{ $viewingApplication->candidate_phone ?: 'N/A' }}</span>
                        </div>
                    </div>

                    @if($viewingApplication->portfolio_url)
                        <div>
                            <span class="font-bold text-slate-700 block mb-1">Portfolio & Work Link:</span>
                            <a href="{{ $viewingApplication->portfolio_url }}" target="_blank" class="text-accent-500 hover:underline font-mono text-xs block truncate">
                                {{ $viewingApplication->portfolio_url }} ↗
                            </a>
                        </div>
                    @endif

                    @if($viewingApplication->cover_letter)
                        <div>
                            <span class="font-bold text-slate-700 block mb-1">Cover Note:</span>
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 leading-relaxed max-h-48 overflow-y-auto">
                                {{ $viewingApplication->cover_letter }}
                            </div>
                        </div>
                    @endif

                    <div class="pt-3 border-t border-slate-100">
                        <label class="block font-bold text-slate-700 mb-2">Update Recruitment Stage:</label>
                        <div class="grid grid-cols-5 gap-2 font-mono text-xs">
                            <button wire:click="updateStatus({{ $viewingApplication->id }}, 'new')" class="py-2 px-2 rounded-xl border cursor-pointer {{ $viewingApplication->status === 'new' ? 'bg-accent-500 text-white font-bold' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200' }}">New</button>
                            <button wire:click="updateStatus({{ $viewingApplication->id }}, 'reviewing')" class="py-2 px-2 rounded-xl border cursor-pointer {{ $viewingApplication->status === 'reviewing' ? 'bg-ink-900 text-white font-bold' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200' }}">Reviewing</button>
                            <button wire:click="updateStatus({{ $viewingApplication->id }}, 'shortlisted')" class="py-2 px-2 rounded-xl border cursor-pointer {{ $viewingApplication->status === 'shortlisted' ? 'bg-emerald-600 text-white font-bold' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200' }}">Shortlist</button>
                            <button wire:click="updateStatus({{ $viewingApplication->id }}, 'rejected')" class="py-2 px-2 rounded-xl border cursor-pointer {{ $viewingApplication->status === 'rejected' ? 'bg-rose-600 text-white font-bold' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200' }}">Reject</button>
                            <button wire:click="updateStatus({{ $viewingApplication->id }}, 'hired')" class="py-2 px-2 rounded-xl border cursor-pointer {{ $viewingApplication->status === 'hired' ? 'bg-purple-600 text-white font-bold' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200' }}">Hire 🎉</button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <button wire:click="delete({{ $viewingApplication->id }})" onclick="return confirm('Remove applicant?') || event.stopImmediatePropagation()" class="text-rose-600 font-bold text-xs hover:underline">
                        Delete Applicant
                    </button>
                    <button wire:click="$set('showDetailModal', false)" class="px-5 py-2.5 bg-ink-900 text-white font-bold rounded-xl text-xs uppercase tracking-wider">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
