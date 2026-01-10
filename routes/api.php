<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\GHNWebhookController;
use App\Http\Controllers\Webhook\DepositWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhook/ghn', [GHNWebhookController::class, 'handleWebhook']);
Route::post('/hooks/deposit', [DepositWebhookController::class, '__invoke']);
