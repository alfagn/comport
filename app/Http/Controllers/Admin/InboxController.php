<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Services\ContactMessageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Dashboard inbox for contact briefs and newsletter subscribers.
 */
class InboxController extends Controller
{
    public function __construct(private ContactMessageService $messages) {}

    /** List briefs, unread first. */
    public function index(): View
    {
        return view('admin.inbox.index', ['items' => $this->messages->inbox()]);
    }

    /** Open one brief (marks it read). */
    public function show(int $id): View
    {
        return view('admin.inbox.show', ['message' => $this->messages->openAndMarkRead($id)]);
    }

    /** Delete a brief. */
    public function destroy(int $id): RedirectResponse
    {
        $this->messages->delete($id);

        return redirect()->route('admin.inbox.index')->with('success', 'Message deleted.');
    }

    /** List newsletter subscribers. */
    public function subscribers(): View
    {
        return view('admin.inbox.subscribers', ['items' => NewsletterSubscriber::latest()->paginate(20)]);
    }
}
