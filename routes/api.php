<?php

use App\Http\Controllers\Api\WhatsappWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Stateless External Webhooks & Customer APIs
|--------------------------------------------------------------------------
| Mounted under /api without session cookies or CSRF tokens. Signature
| verification happens inside the webhook controller.
*/

Route::prefix('webhooks/whatsapp')->name('api.webhooks.whatsapp.')->group(function () {
    Route::get('/', [WhatsappWebhookController::class, 'verify'])->name('verify');
    Route::post('/', [WhatsappWebhookController::class, 'handle'])->name('handle');
});
