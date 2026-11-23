<?php

namespace App\Http\Controllers\Public;

use App\DTOs\ContactMessageData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ContactRequest;
use App\Services\ContactMessageService;
use Illuminate\Http\RedirectResponse;

/**
 * Receives project briefs from the public contact form.
 */
class ContactMessageController extends Controller
{
    public function __construct(private ContactMessageService $messages) {}

    /** Validate and store a brief, then bounce back with a thank-you flash. */
    public function store(ContactRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // The public form names its fields differently from the table.
        $validated['brief'] = $validated['description'];
        $validated['services'] = $validated['service'] ?? [];
        unset($validated['description'], $validated['service']);

        $this->messages->storeFromPublic(ContactMessageData::fromArray($validated));

        return back()->with('success', 'Thank you. Your brief has been delivered to our co-founders; we reply within 24 business hours.');
    }
}
