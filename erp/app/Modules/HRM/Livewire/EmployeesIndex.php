<?php

declare(strict_types=1);

namespace App\Modules\HRM\Livewire;

use App\Core\Livewire\BaseTable;
use App\Modules\HRM\Models\Department;
use App\Modules\HRM\Models\Employee;

/**
 * Class EmployeesIndex
 *
 * Livewire component managing employees directory, department filters, and employee salaries.
 */
class EmployeesIndex extends BaseTable
{
    public string $departmentFilter = '';

    public function render()
    {
        $departments = Department::on('tenant')->get();

        $employees = Employee::on('tenant')
            ->with(['department', 'designation'])
            ->when($this->search !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('employee_code', 'like', "%{$this->search}%");
                });
            })
            ->when($this->departmentFilter !== '', fn ($q) => $q->where('department_id', $this->departmentFilter))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('hrm::livewire.employees-index', [
            'employees' => $employees,
            'departments' => $departments,
        ])->layout('layouts.tenant', [
            'title' => 'Employees Directory',
            'header' => app()->getLocale() === 'ar' ? 'سجل الموظفين' : 'Employees Directory & Profiles',
        ]);
    }
}
