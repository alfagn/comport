<?php

declare(strict_types=1);

namespace App\Modules\HRM\Services;

use App\Core\Services\BaseService;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\Payroll;
use App\Modules\HRM\Models\PayrollItem;
use Throwable;

/**
 * Class PayrollCalculationService
 *
 * Domain service calculating monthly payroll, itemizing allowances/deductions,
 * and maintaining exact financial precision.
 */
class PayrollCalculationService extends BaseService
{
    /**
     * Generate payroll for an employee for a specific month and year.
     *
     * @param  array<int, array{type: string, title: string, amount_minor: int}>  $customItems
     *
     * @throws Throwable
     */
    public function generate(Employee $employee, int $month, int $year, array $customItems = []): Payroll
    {
        return $this->transaction(function () use ($employee, $month, $year, $customItems) {
            $basicSalaryMinor = $employee->basic_salary_minor;
            $allowancesMinor = 0;
            $deductionsMinor = 0;

            foreach ($customItems as $item) {
                if ($item['type'] === 'allowance') {
                    $allowancesMinor += (int) $item['amount_minor'];
                } elseif ($item['type'] === 'deduction') {
                    $deductionsMinor += (int) $item['amount_minor'];
                }
            }

            $netSalaryMinor = max(0, $basicSalaryMinor + $allowancesMinor - $deductionsMinor);

            /** @var Payroll $payroll */
            $payroll = Payroll::on('tenant')->updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'month' => $month,
                    'year' => $year,
                ],
                [
                    'basic_salary_minor' => $basicSalaryMinor,
                    'allowances_total_minor' => $allowancesMinor,
                    'deductions_total_minor' => $deductionsMinor,
                    'net_salary_minor' => $netSalaryMinor,
                    'currency' => $employee->currency ?: 'EGP',
                    'status' => 'draft',
                ]
            );

            // Re-sync line items
            $payroll->items()->delete();
            foreach ($customItems as $item) {
                PayrollItem::on('tenant')->create([
                    'payroll_id' => $payroll->id,
                    'type' => $item['type'],
                    'title' => $item['title'],
                    'amount_minor' => (int) $item['amount_minor'],
                ]);
            }

            $this->logInfo('Generated payroll', [
                'employee_id' => $employee->id,
                'month' => $month,
                'year' => $year,
                'net_salary_minor' => $netSalaryMinor,
            ]);

            return $payroll;
        }, connection: 'tenant');
    }
}
