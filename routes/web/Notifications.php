<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserNotificationController;

Route::middleware('auth')->group(function () {
    Route::get('/user-notifications', [UserNotificationController::class, 'index'])->name('user-notifications.index');
    Route::patch('/user-notifications/{userNotification}/read', [UserNotificationController::class, 'markAsRead'])->name('user-notifications.markAsRead');
    Route::delete('/user-notifications/{userNotification}', [UserNotificationController::class, 'destroy'])->name('user-notifications.destroy');
});

