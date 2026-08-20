<?php

use Illuminate\Support\Facades\Route;
use Modules\Instagram\Http\Controllers\InstagramWebhookController;

Route::get(
    '/instagram/webhook',
    [InstagramWebhookController::class, 'verify']
)->name('instagram.webhook.verify');

Route::post(
    '/instagram/webhook',
    [InstagramWebhookController::class, 'handle']
)->name('instagram.webhook.handle');
