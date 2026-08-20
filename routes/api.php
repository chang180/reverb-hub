<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\HubApiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth.hub.api', 'throttle:60,1'])->group(function () {
    Route::get('/', HubApiController::class)
        ->middleware('hub.api.ability:docs:read');

    Route::middleware('hub.api.ability:applications:create')
        ->post('applications', [ApplicationController::class, 'store']);

    Route::middleware('hub.api.ability:applications:read')
        ->get('applications', [ApplicationController::class, 'index']);

    Route::middleware('hub.api.ability:applications:update')
        ->patch('applications/{application}', [ApplicationController::class, 'update']);

    Route::middleware('hub.api.ability:applications:update')
        ->post('applications/{application}/rotate', [ApplicationController::class, 'rotate']);

    Route::middleware('hub.api.ability:applications:delete')
        ->delete('applications/{application}', [ApplicationController::class, 'destroy']);
});
