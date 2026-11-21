<div>
    <x-ui.card :title="app()->getLocale() === 'ar' ? 'فواتير العملاء' : 'Commercial Invoices'">
        <div class="mb-6 flex flex-col sm:flex-row items-center gap-4">
            <div class="w-full sm:w-72">
                <x-ui.input wire:model.live.debounce.300ms="search" placeholder="Search invoice number or client..." />
            </div>

            <div class="w-full sm:w-48">
                <x-ui.select wire:model.live="statusFilter">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All Statuses' }}</option>
                    <option value="paid">Paid</option>
                    <option value="partial">Partial</option>
                    <option value="draft">Draft</option>
                </x-ui.select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs text-slate-400 uppercase font-semibold">
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'رقم الفاتورة' : 'Invoice #' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'العميل' : 'Client' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'تاريخ الاستحقاق' : 'Due Date' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الإجمالي' : 'Total' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'المسدد' : 'Paid' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'المتبقي' : 'Balance Due' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th class="pb-3 text-end">{{ app()->getLocale() === 'ar' ? 'الإجراء' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($invoices as $inv)
                        <tr>
                            <td class="py-3.5 font-mono text-xs font-bold text-slate-900 dark:text-white">
                                {{ $inv->invoice_number }}
                            </td>
                            <td class="py-3.5 font-bold">
                                {{ $inv->client?->name }}
                            </td>
                            <td class="py-3.5 text-xs text-slate-500">
                                {{ $inv->due_date?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="py-3.5 font-bold text-slate-900 dark:text-white">
                                {{ $inv->total->format() }}
                            </td>
                            <td class="py-3.5 font-medium text-emerald-600">
                                {{ number_format($inv->amount_paid_minor / 100, 2) }} {{ $inv->currency }}
                            </td>
                            <td class="py-3.5 font-bold text-rose-600">
                                {{ $inv->balance_due->format() }}
                            </td>
                            <td class="py-3.5">
                                <x-ui.badge :variant="$inv->status === 'paid' ? 'success' : ($inv->status === 'partial' ? 'warning' : 'neutral')">
                                    {{ ucfirst($inv->status) }}
                                </x-ui.badge>
                            </td>
                            <td class="py-3.5 text-end">
                                @if(! $inv->isFullyPaid())
                                    <button
                                        wire:click="recordPayment({{ $inv->id }})"
                                        class="px-2.5 py-1 text-xs font-bold rounded bg-emerald-600 text-white hover:bg-emerald-700"
                                    >
                                        💳 {{ app()->getLocale() === 'ar' ? 'تسجيل سداد' : 'Record Pay' }}
                                    </button>
                                @else
                                    <span class="text-xs text-emerald-600 font-bold">✓ Settled</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-500">No invoices generated yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
    </x-ui.card>
</div>
