<div>
    <x-ui.card :title="app()->getLocale() === 'ar' ? 'المشاريع والمهام الهندسية' : 'Software Engineering Projects'">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs text-slate-400 uppercase font-semibold">
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'المشروع' : 'Project' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'العميل' : 'Client' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الميزانية' : 'Budget' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'المهام' : 'Tasks' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الموعد النهائي' : 'Deadline' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($projects as $p)
                        <tr>
                            <td class="py-3.5 font-bold text-slate-900 dark:text-white">
                                {{ $p->name }}
                            </td>
                            <td class="py-3.5 text-xs text-slate-600 dark:text-slate-300">
                                {{ $p->client?->name ?? 'Internal' }}
                            </td>
                            <td class="py-3.5 font-bold text-slate-900 dark:text-white">
                                {{ $p->budget->format() }}
                            </td>
                            <td class="py-3.5 text-xs">
                                <span class="font-bold text-indigo-600">{{ $p->tasks_count }}</span> {{ app()->getLocale() === 'ar' ? 'مهام' : 'tasks' }}
                            </td>
                            <td class="py-3.5 text-xs text-slate-500">
                                {{ $p->deadline?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="py-3.5">
                                <x-ui.badge :variant="$p->status === 'in_progress' ? 'primary' : 'neutral'">
                                    {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                </x-ui.badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">No projects registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
</div>
