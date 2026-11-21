<div class="space-y-6 max-w-7xl mx-auto font-sans">
    <!-- Executive Header Card -->
    <div class="bg-gradient-to-r from-[#050C1A] via-[#08142C] to-[#0D1E3D] border border-white/10 rounded-2xl p-6 sm:p-8 text-white shadow-card flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-accent-500/20 text-accent-500 border border-accent-500/30 text-[11px] font-mono font-bold uppercase tracking-wider mb-2">
                <span class="w-2 h-2 rounded-full bg-accent-500"></span>
                <span>Talent Acquisition & Careers Portal</span>
            </div>
            <h2 class="text-2xl font-heading font-black tracking-tight">
                {{ app()->getLocale() === 'ar' ? 'بوابة الوظائف والشواغر المتاحة (Careers & Vacancies)' : 'Careers & Job Openings Management' }}
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 mt-1">
                {{ app()->getLocale() === 'ar' ? 'إدارة الوظائف المعروضة على بوابة التوظيف (/careers) وشروط كل وظيفة واستقبال طلبات المتقدمين.' : 'Manage open engineering, design, and marketing vacancies on /careers.' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <a href="{{ route('admin.cms.applications') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/15 text-xs font-bold rounded-xl transition-all">
                {{ app()->getLocale() === 'ar' ? 'صندوق طلبات التوظيف (CVs) →' : 'Applications Inbox →' }}
            </a>
            <button wire:click="create"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-accent-500 to-[#ff6b42] hover:from-accent-600 hover:to-accent-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-glow transition-all cursor-pointer">
                <span>+ {{ app()->getLocale() === 'ar' ? 'إضافة وظيفة جديدة' : 'New Job Vacancy' }}</span>
            </button>
        </div>
    </div>

    <!-- Jobs Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($jobs as $job)
            <div class="bg-white border {{ $job->is_active ? 'border-slate-200/80' : 'border-slate-200 opacity-60' }} rounded-2xl p-6 flex flex-col justify-between shadow-subtle hover:border-accent-500 hover:shadow-card transition-all">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-ink-900 font-mono text-[10px] font-bold">
                            {{ $job->department }}
                        </span>
                        <span class="font-mono text-[10px] text-slate-400 uppercase font-bold">
                            {{ str_replace('_', ' ', $job->employment_type) }}
                        </span>
                    </div>

                    <div>
                        <h3 class="font-heading font-extrabold text-ink-900 text-base leading-snug">{{ $job->title }}</h3>
                        <a href="{{ route('site.job.detail', $job->slug) }}" target="_blank" class="text-[11px] text-slate-400 hover:text-accent-500 font-mono mt-0.5 inline-block">
                            {{ $job->location }} • /careers/{{ $job->slug }} ↗
                        </a>
                    </div>

                    <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                        {{ $job->description }}
                    </p>

                    <div class="pt-2 flex items-center justify-between text-xs font-mono text-slate-500">
                        <span>Comp: <strong class="text-ink-900">{{ $job->salary_range ?: 'Competitive' }}</strong></span>
                        <span class="px-2 py-0.5 rounded-full bg-accent-50 text-accent-600 font-bold text-[10px]">{{ $job->applications_count }} applicants</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 mt-5 flex items-center justify-between text-xs">
                    <button wire:click="toggleActive({{ $job->id }})" class="font-mono text-[10px] font-bold px-2.5 py-1 rounded-full border cursor-pointer {{ $job->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                        {{ $job->is_active ? '● OPEN' : '○ CLOSED' }}
                    </button>

                    <div class="flex items-center gap-2">
                        <button wire:click="edit({{ $job->id }})" class="px-3.5 py-1.5 text-ink-900 hover:text-white hover:bg-ink-900 font-bold border border-slate-200 rounded-xl bg-white transition-all cursor-pointer">
                            Edit
                        </button>
                        <button wire:click="delete({{ $job->id }})" onclick="return confirm('Delete vacancy?') || event.stopImmediatePropagation()" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-xl cursor-pointer">
                            ✕
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-14 text-center bg-white border border-slate-200 rounded-2xl text-slate-400">
                No vacancies created yet.
            </div>
        @endforelse
    </div>

    <!-- Create / Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-950/75 backdrop-blur-xs">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-2xl p-6 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-heading font-extrabold text-ink-900">
                        {{ $editingId ? 'Edit Job Posting' : 'Post New Career Opportunity' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-700">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Job Title</label>
                            <input type="text" wire:model="title" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl" placeholder="Senior Laravel Engineer" />
                            @error('title') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Slug (URL)</label>
                            <input type="text" wire:model="slug" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono" placeholder="senior-laravel-engineer" />
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Department</label>
                            <input type="text" wire:model="department" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl" placeholder="Engineering" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Employment Type</label>
                            <select wire:model="employment_type" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono">
                                <option value="full_time">Full Time</option>
                                <option value="part_time">Part Time</option>
                                <option value="contract">Contract</option>
                                <option value="remote">Remote</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Salary Range</label>
                            <input type="text" wire:model="salary_range" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono" placeholder="Competitive + Equity" />
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Location</label>
                        <input type="text" wire:model="location" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl" placeholder="Cairo HQ / Remote EMEA" />
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Job Description & Scope</label>
                        <textarea rows="4" wire:model="description" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl"></textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Requirements & Qualifications</label>
                        <textarea rows="4" wire:model="requirements" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl"></textarea>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <label class="flex items-center gap-2.5 cursor-pointer font-bold text-ink-900">
                            <input type="checkbox" wire:model="is_active" class="rounded border-slate-300 text-accent-500 focus:ring-accent-500">
                            <span>Open for Applicants (Active)</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2.5 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button wire:click="save" class="px-5 py-2.5 bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow-glow">
                        Save Vacancy ✓
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
