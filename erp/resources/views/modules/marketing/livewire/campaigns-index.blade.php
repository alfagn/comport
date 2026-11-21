<div>
    <x-ui.card :title="app()->getLocale() === 'ar' ? 'حملات التسويق الرقمي والأداء' : 'Active Digital Ad Campaigns'">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs text-slate-400 uppercase font-semibold">
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الحملة' : 'Campaign' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'العميل' : 'Client Account' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'القناة' : 'Channel' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الميزانية' : 'Budget' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'المنفق' : 'Spent' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'النتائج المحققة' : 'Results / CPL' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($campaigns as $camp)
                        @php
                        $latestKpi = $camp->kpiLogs->last();
                        @endphp
                        <tr>
                            <td class="py-3.5 font-bold text-slate-900 dark:text-white">
                                {{ $camp->name }}
                            </td>
                            <td class="py-3.5 text-xs text-slate-600 dark:text-slate-300">
                                {{ $camp->client?->name ?? 'Internal Campaign' }}
                            </td>
                            <td class="py-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-indigo-50 dark:bg-indigo-950 text-indigo-600">
                                    {{ $camp->channel }}
                                </span>
                            </td>
                            <td class="py-3.5 font-bold text-slate-900 dark:text-white">
                                {{ $camp->budget->format() }}
                            </td>
                            <td class="py-3.5 font-bold text-slate-600 dark:text-slate-400">
                                {{ $camp->spent->format() }}
                            </td>
                            <td class="py-3.5 text-xs">
                                @if($latestKpi)
                                    <p class="font-bold text-emerald-600">{{ $latestKpi->leads_generated }} Leads • {{ $latestKpi->conversions }} Conversions</p>
                                    <p class="text-slate-400 text-[11px] font-mono">CPL: {{ $latestKpi->cost_per_lead }} EGP</p>
                                @else
                                    <span class="text-slate-400">Pending data</span>
                                @endif
                            </td>
                            <td class="py-3.5">
                                <x-ui.badge :variant="$camp->status === 'active' ? 'success' : 'neutral'">
                                    {{ ucfirst($camp->status) }}
                                </x-ui.badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">No campaigns found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
</div>
