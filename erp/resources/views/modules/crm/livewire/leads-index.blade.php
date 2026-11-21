<div>
    <x-ui.card :title="app()->getLocale() === 'ar' ? 'العملاء المحتملين وفرص المبيعات' : 'Sales Leads Pipeline'">
        <div class="mb-6 flex flex-col sm:flex-row items-center gap-4">
            <div class="w-full sm:w-72">
                <x-ui.input wire:model.live.debounce.300ms="search" placeholder="Search lead or contact..." />
            </div>

            <div class="w-full sm:w-56">
                <x-ui.select wire:model.live="statusFilter">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'كل المراحل' : 'All Stages' }}</option>
                    <option value="new">New</option>
                    <option value="contacted">Contacted</option>
                    <option value="qualified">Qualified</option>
                    <option value="proposal_sent">Proposal Sent</option>
                    <option value="won">Won</option>
                    <option value="lost">Lost</option>
                </x-ui.select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs text-slate-400 uppercase font-semibold">
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الفرصة' : 'Opportunity' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'جهة الاتصال' : 'Contact Person' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'المصدر' : 'Source' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'القيمة التقديرية' : 'Estimated Value' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'المرحلة' : 'Stage' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($leads as $lead)
                        <tr>
                            <td class="py-3.5 font-bold text-slate-900 dark:text-white">
                                {{ $lead->title }}
                            </td>
                            <td class="py-3.5">
                                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $lead->contact_name }}</p>
                                <p class="text-xs text-slate-500">{{ $lead->phone }} • {{ $lead->email }}</p>
                            </td>
                            <td class="py-3.5 text-xs capitalize text-slate-600 dark:text-slate-400">
                                {{ $lead->source }}
                            </td>
                            <td class="py-3.5 font-bold text-slate-900 dark:text-white">
                                {{ $lead->estimated_value->format() }}
                            </td>
                            <td class="py-3.5">
                                <select
                                    wire:change="updateLeadStatus({{ $lead->id }}, $event.target.value)"
                                    class="text-xs font-semibold rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 py-1 px-2"
                                >
                                    <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>New</option>
                                    <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                    <option value="qualified" {{ $lead->status === 'qualified' ? 'selected' : '' }}>Qualified</option>
                                    <option value="proposal_sent" {{ $lead->status === 'proposal_sent' ? 'selected' : '' }}>Proposal Sent</option>
                                    <option value="won" {{ $lead->status === 'won' ? 'selected' : '' }}>Won ✓</option>
                                    <option value="lost" {{ $lead->status === 'lost' ? 'selected' : '' }}>Lost ✕</option>
                                </select>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">No leads recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $leads->links() }}
        </div>
    </x-ui.card>
</div>
