<div>
    <x-ui.card :title="app()->getLocale() === 'ar' ? 'سجل الموظفين والرواتب' : 'Employees Directory'">
        <!-- Filter bar -->
        <div class="mb-6 flex flex-col sm:flex-row items-center gap-4">
            <div class="w-full sm:w-72">
                <x-ui.input wire:model.live.debounce.300ms="search" placeholder="Search employee or code..." />
            </div>

            <div class="w-full sm:w-60">
                <x-ui.select wire:model.live="departmentFilter">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'كل الأقسام' : 'All Departments' }}</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </x-ui.select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs text-slate-400 uppercase font-semibold">
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الموظف' : 'Employee' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'القسم والمسمى' : 'Dept & Role' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الراتب الأساسي' : 'Basic Salary' }}</th>
                        <th class="pb-3 text-start">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($employees as $emp)
                        <tr>
                            <td class="py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-950 text-indigo-600 font-bold flex items-center justify-center text-xs">
                                        {{ mb_substr($emp->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-white">{{ $emp->full_name }}</p>
                                        <p class="text-xs text-slate-500 font-mono">{{ $emp->employee_code }} • {{ $emp->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $emp->department?->name ?? '—' }}</p>
                                <p class="text-xs text-slate-500">{{ $emp->designation?->name ?? '—' }}</p>
                            </td>
                            <td class="py-3.5 font-bold text-slate-900 dark:text-white">
                                {{ $emp->basic_salary->format() }}
                            </td>
                            <td class="py-3.5">
                                <x-ui.badge :variant="$emp->employment_status === 'active' ? 'success' : 'neutral'">
                                    {{ ucfirst($emp->employment_status) }}
                                </x-ui.badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-500">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $employees->links() }}
        </div>
    </x-ui.card>
</div>
