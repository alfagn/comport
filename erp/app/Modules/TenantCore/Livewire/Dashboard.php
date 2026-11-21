<?php

declare(strict_types=1);

namespace App\Modules\TenantCore\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Modules\CRM\Models\CrmLead;
use App\Modules\Finance\Models\Invoice;
use App\Modules\HRM\Models\Attendance;
use App\Modules\HRM\Models\Employee;
use App\Modules\Marketing\Models\MarketingCampaign;
use App\Modules\Projects\Models\Project;
use App\Modules\Projects\Models\Task;
use App\Modules\Tenancy\Models\Tenant;
use Carbon\Carbon;

/**
 * Class Dashboard
 *
 * Tenant ERP Company Workspace main operational dashboard.
 */
class Dashboard extends BaseComponent
{
    public function render()
    {
        $tenant = Tenant::current();

        // HRM Metrics
        $totalEmployees = Employee::on('tenant')->where('employment_status', 'active')->count();
        $presentToday = Attendance::on('tenant')->where('date', Carbon::today())->where('status', 'present')->count();

        // CRM Metrics
        $leadsCount = CrmLead::on('tenant')->count();
        $pipelineValueMinor = (int) CrmLead::on('tenant')->sum('estimated_value_minor');

        // Marketing Metrics
        $activeCampaigns = MarketingCampaign::on('tenant')->where('status', 'active')->count();
        $totalMarketingSpendMinor = (int) MarketingCampaign::on('tenant')->sum('spent_minor');

        // Projects Metrics
        $activeProjects = Project::on('tenant')->whereIn('status', ['planning', 'in_progress'])->count();
        $openTasks = Task::on('tenant')->whereIn('status', ['todo', 'in_progress', 'in_review'])->count();

        // Finance Metrics
        $totalInvoicedMinor = (int) Invoice::on('tenant')->sum('total_minor');
        $paidInvoicedMinor = (int) Invoice::on('tenant')->sum('amount_paid_minor');

        return view('tenant::livewire.dashboard', [
            'tenant' => $tenant,
            'totalEmployees' => $totalEmployees,
            'presentToday' => $presentToday,
            'leadsCount' => $leadsCount,
            'pipelineValueMinor' => $pipelineValueMinor,
            'activeCampaigns' => $activeCampaigns,
            'totalMarketingSpendMinor' => $totalMarketingSpendMinor,
            'activeProjects' => $activeProjects,
            'openTasks' => $openTasks,
            'totalInvoicedMinor' => $totalInvoicedMinor,
            'paidInvoicedMinor' => $paidInvoicedMinor,
        ])->layout('layouts.tenant', [
            'title' => ($tenant ? $tenant->name : 'Company').' Dashboard',
            'header' => app()->getLocale() === 'ar' ? 'لوحة تحكم الشركة ومتابعة العمليات' : 'Company Operations Overview',
        ]);
    }
}
