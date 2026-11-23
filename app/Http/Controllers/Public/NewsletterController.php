<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\SubscribeRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;

/**
 * Receives footer newsletter subscriptions.
 */
class NewsletterController extends Controller
{
    /** Store the subscription (idempotent per email) and bounce back. */
    public function store(SubscribeRequest $request): RedirectResponse
    {
        NewsletterSubscriber::firstOrCreate(['email' => $request->validated('email')]);

        return back()->with('subscribed', 'Subscribed to Empetix Solutions Insights.');
    }
}
