<div>
    <x-ui.card :title="app()->getLocale() === 'ar' ? 'فواتير الاشتراكات والتحويلات' : 'Subscription Invoices & Receipts'">
        <div class="mb-6 flex gap-2">
            <button wire:click="$set('statusFilter', 'pending')" class="px-3 py-1.5 rounded-lg text-xs font-bold border {{ $statusFilter === 'pending' ? 'bg-indigo-600 text-white border-transparent' : 'border-slate-200 dark:border-slate-800' }}">
                {{ app()->getLocale() === 'ar' ? 'بانتظار الاعتماد' : 'Pending Approvals' }}
            </button>
            <button wire:click="$set('statusFilter', 'paid')" class="px-3 py-1.5 rounded-lg text-xs font-bold border {{ $statusFilter === 'paid' ? 'bg-indigo-600 text-white border-transparent' : 'border-slate-200 dark:border-slate-800' }}">
                {{ app()->getLocale() === 'ar' ? 'المعتمدة والمسددة' : 'Paid & Approved' }}
            </button>
            <button wire:click="$set('statusFilter', '')" class="px-3 py-1.5 rounded-lg text-xs font-bold border {{ $statusFilter === '' ? 'bg-indigo-600 text-white border-transparent' : 'border-slate-200 dark:border-slate-800' }}">
                {{ app()->getLocale() === 'ar' ? 'الكل' : 'All Invoices' }}
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs text-slate-400 uppercase font-semibold">
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'رقم الفاتورة' : 'Invoice #' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الشركة' : 'Company' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الخطة' : 'Plan' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'المبلغ' : 'Amount' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'طريقة الدفع' : 'Method' }}</th>
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
                            <td class="py-3.5 font-medium">
                                {{ $inv->tenant?->name }}
                            </td>
                            <td class="py-3.5 text-xs text-slate-500">
                                {{ $inv->subscription?->plan?->name ?? 'Plan' }}
                            </td>
                            <td class="py-3.5 font-bold text-slate-900 dark:text-white">
                                {{ $inv->amount->format() }}
                            </td>
                            <td class="py-3.5 text-xs text-slate-500 capitalize">
                                {{ str_replace('_', ' ', $inv->payment_method) }}
                            </td>
                            <td class="py-3.5">
                                <x-ui.badge :variant="$inv->status === 'paid' ? 'success' : ($inv->status === 'pending' ? 'warning' : 'danger')">
                                    {{ ucfirst($inv->status) }}
                                </x-ui.badge>
                            </td>
                            <td class="py-3.5 text-end">
                                @if($inv->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            wire:click="approvePayment({{ $inv->id }})"
                                            class="px-2.5 py-1 text-xs font-bold rounded bg-emerald-600 text-white hover:bg-emerald-700"
                                        >
                                            ✓ {{ app()->getLocale() === 'ar' ? 'اعتماد وتفعيل' : 'Approve' }}
                                        </button>
                                        <button
                                            wire:click="rejectPayment({{ $inv->id }})"
                                            class="px-2.5 py-1 text-xs font-bold rounded bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">
                                        {{ $inv->paid_at?->format('d M Y') ?? '—' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">No invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
</div>
