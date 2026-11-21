<?php

declare(strict_types=1);

namespace App\Modules\HRM\Livewire;

use App\Core\Livewire\BaseTable;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\Payroll;
use App\Modules\HRM\Services\PayrollCalculationService;
use Carbon\Carbon;

/**
 * Class PayrollIndex
 *
 * Livewire component managing monthly company payroll calculation, approvals, and payslips.
 */
class PayrollIndex extends BaseTable
{
    public int $selectedMonth;

    public int $selectedYear;

    public function mount(): void
    {
        $this->selectedMonth = (int) Carbon::now()->format('m');
        $this->selectedYear = (int) Carbon::now()->format('Y');
    }

    public function generateAllPayroll(PayrollCalculationService $payrollService): void
    {
        $employees = Employee::on('tenant')->where('employment_status', 'active')->get();

        foreach ($employees as $employee) {
            $payrollService->generate($employee, $this->selectedMonth, $this->selectedYear);
        }

        $this->toastSuccess('Monthly payroll generated for active employees!');
    }

    public function render()
    {
        $payrolls = Payroll::on('tenant')
            ->with(['employee.designation', 'items'])
            ->where('month', $this->selectedMonth)
            ->where('year', $this->selectedYear)
            ->paginate($this->perPage);

        return view('hrm::livewire.payroll-index', [
            'payrolls' => $payrolls,
        ])->layout('layouts.tenant', [
            'title' => 'Payroll & Payslips',
            'header' => app()->getLocale() === 'ar' ? 'مسيرات الرواتب الشهرية' : 'Monthly Salary Payroll',
        ]);
    }
}
