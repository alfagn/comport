<?php

declare(strict_types=1);

use App\Modules\HRM\Models\Department;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Services\PayrollCalculationService;
use App\Modules\Tenancy\Models\Tenant;

it('calculates employee monthly payroll with exact financial minor precision', function () {
    $tenant = Tenant::on('landlord')->where('slug', 'acme')->first();
    if ($tenant === null) {
        $this->markTestSkipped('Acme tenant not seeded.');
    }

    $tenant->makeCurrent();

    $dept = Department::on('tenant')->firstOrCreate(['name' => 'QA Engineering', 'code' => 'QA']);

    $employee = Employee::on('tenant')->create([
        'employee_code' => 'TEST-EMP-'.time(),
        'first_name' => 'Ahmed',
        'last_name' => 'Mostafa',
        'email' => 'ahmed.'.time().'@acme.com',
        'basic_salary_minor' => 2500000, // 25,000 EGP
        'currency' => 'EGP',
        'department_id' => $dept->id,
    ]);

    $service = app(PayrollCalculationService::class);

    $payroll = $service->generate(
        employee: $employee,
        month: 11,
        year: 2026,
        customItems: [
            ['type' => 'allowance', 'title' => 'Travel Allowance', 'amount_minor' => 200000], // +2,000
            ['type' => 'allowance', 'title' => 'Project Bonus', 'amount_minor' => 150000],    // +1,500
            ['type' => 'deduction', 'title' => 'Social Insurance', 'amount_minor' => 100000], // -1,000
        ]
    );

    // Basic: 25,000 + 3,500 - 1,000 = 27,500 EGP (2750000 minor units)
    expect($payroll->basic_salary_minor)->toBe(2500000);
    expect($payroll->allowances_total_minor)->toBe(350000);
    expect($payroll->deductions_total_minor)->toBe(100000);
    expect($payroll->net_salary_minor)->toBe(2750000);
    expect($payroll->net_salary->toDecimal())->toBe(27500.0);

    $tenant->forgetCurrent();
});
