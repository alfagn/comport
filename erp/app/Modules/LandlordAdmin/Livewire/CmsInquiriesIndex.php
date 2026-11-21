<?php

declare(strict_types=1);

namespace App\Modules\LandlordAdmin\Livewire;

use App\Core\Livewire\BaseTable;
use App\Modules\CMS\Models\ContactMessage;

/**
 * Class CmsInquiriesIndex
 *
 * Livewire component managing client contact messages and leads from the website.
 */
class CmsInquiriesIndex extends BaseTable
{
    public bool $showDetailModal = false;

    public ?ContactMessage $viewingMessage = null;

    public string $filter = 'all'; // all, unread, replied

    public function view(int $id): void
    {
        $this->viewingMessage = ContactMessage::on('landlord')->findOrFail($id);
        $this->viewingMessage->update(['is_read' => true]);
        $this->showDetailModal = true;
    }

    public function toggleReplied(int $id): void
    {
        $msg = ContactMessage::on('landlord')->findOrFail($id);
        $msg->update(['is_replied' => ! $msg->is_replied]);

        if ($this->viewingMessage && $this->viewingMessage->id === $id) {
            $this->viewingMessage->refresh();
        }

        $this->toastSuccess('Reply status toggled');
    }

    public function delete(int $id): void
    {
        ContactMessage::on('landlord')->where('id', $id)->delete();
        $this->showDetailModal = false;
        $this->toastSuccess('Message deleted');
    }

    public function render()
    {
        $query = ContactMessage::on('landlord')
            ->when($this->search !== '', function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('subject', 'like', "%{$this->search}%");
            })
            ->when($this->filter === 'unread', fn ($q) => $q->where('is_read', false))
            ->when($this->filter === 'replied', fn ($q) => $q->where('is_replied', true))
            ->latest();

        $messages = $query->paginate($this->perPage);

        return view('admin::livewire.cms-inquiries-index', [
            'messages' => $messages,
            'totalCount' => ContactMessage::on('landlord')->count(),
            'unreadCount' => ContactMessage::on('landlord')->where('is_read', false)->count(),
        ])->layout('layouts.admin', [
            'title' => 'Client Inquiries & Messages — Empetix CMS',
            'header' => app()->getLocale() === 'ar' ? 'رسائل واستفسارات العملاء' : 'Client Inquiries & Project Messages',
        ]);
    }
}
