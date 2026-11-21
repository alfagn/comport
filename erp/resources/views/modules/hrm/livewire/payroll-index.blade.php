<div>
    <x-ui.card :title="app()->getLocale() === 'ar' ? 'مسير رواتب شهر ' . $selectedMonth . ' / ' . $selectedYear : 'Payroll for ' . $selectedMonth . '/' . $selectedYear">
        <x-slot:actions>
            <button
                wire:click="generateAllPayroll"
                class="px-3.5 py-1.5 text-xs font-bold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs"
            >
                ⚡ {{ app()->getLocale() === 'ar' ? 'حساب مرتبات الشهر' : 'Generate Month Payroll' }}
            </button>
        </x-slot:actions>

        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs text-slate-400 uppercase font-semibold">
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الموظف' : 'Employee' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الأساسي' : 'Basic' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'البدلات والمكافآت' : 'Allowances' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الخصومات والتأمينات' : 'Deductions' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'صافي الراتب' : 'Net Salary' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($payrolls as $pr)
                        <tr>
                            <td class="py-3.5 font-bold text-slate-900 dark:text-white">
                                {{ $pr->employee?->full_name }}
                            </td>
                            <td class="py-3.5 text-xs font-medium">
                                {{ $pr->basic_salary->format() }}
                            </td>
                            <td class="py-3.5 text-xs font-semibold text-emerald-600">
                                +{{ number_format($pr->allowances_total_minor / 100, 2) }}
                            </td>
                            <td class="py-3.5 text-xs font-semibold text-rose-600">
                                -{{ number_format($pr->deductions_total_minor / 100, 2) }}
                            </td>
                            <td class="py-3.5 text-sm font-extrabold text-indigo-600 dark:text-indigo-400">
                                {{ $pr->net_salary->format() }}
                            </td>
                            <td class="py-3.5">
                                <x-ui.badge :variant="$pr->status === 'paid' ? 'success' : 'neutral'">
                                    {{ ucfirst($pr->status) }}
                                </x-ui.badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">
                                {{ app()->getLocale() === 'ar' ? 'لم يتم حساب المرتبات لهذا الشهر بعد. اضغط حساب مرتبات الشهر.' : 'No payroll entries generated for this period.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
</div>
