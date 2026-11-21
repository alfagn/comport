<?php

declare(strict_types=1);

namespace App\Modules\CRM\Livewire;

use App\Core\Livewire\BaseTable;
use App\Modules\CRM\Models\CrmLead;

/**
 * Class LeadsIndex
 *
 * Livewire component managing digital marketing incoming leads, pipelines, and qualification statuses.
 */
class LeadsIndex extends BaseTable
{
    public string $statusFilter = '';

    public function updateLeadStatus(int $leadId, string $status): void
    {
        $lead = CrmLead::on('tenant')->findOrFail($leadId);
        $lead->update(['status' => $status]);

        $this->toastSuccess('Lead pipeline status updated.');
    }

    public function render()
    {
        $leads = CrmLead::on('tenant')
            ->when($this->search !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('title', 'like', "%{$this->search}%")
                        ->orWhere('contact_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('crm::livewire.leads-index', [
            'leads' => $leads,
        ])->layout('layouts.tenant', [
            'title' => 'CRM Leads & Pipeline',
            'header' => app()->getLocale() === 'ar' ? 'إدارة العملاء المحتملين (Leads)' : 'Leads & Sales Pipeline',
        ]);
    }
}
