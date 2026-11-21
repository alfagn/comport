<?php

declare(strict_types=1);

namespace App\Core\Livewire;

use Livewire\Component;

/**
 * Class BaseComponent
 *
 * Base Livewire Component providing standardized notification,
 * modal management, and localization awareness.
 */
abstract class BaseComponent extends Component
{
    /**
     * Dispatch browser toast notification.
     *
     * @param  string  $type  success|error|warning|info
     */
    protected function toast(string $message, string $type = 'success'): void
    {
        $this->dispatch('toast', [
            'message' => $message,
            'type' => $type,
        ]);
    }

    /**
     * Notify success helper.
     */
    protected function toastSuccess(string $message): void
    {
        $this->toast($message, 'success');
    }

    /**
     * Notify error helper.
     */
    protected function toastError(string $message): void
    {
        $this->toast($message, 'error');
    }
}
