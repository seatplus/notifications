<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Notifications\Http\Controllers\NotificationsController;

Route::middleware(['web', 'auth'])
    ->prefix('notifications')
    ->group(function () {
        Route::get('', [NotificationsController::class, 'index'])->name('notifications.index');
        Route::post('/subscription/current', [NotificationsController::class, 'currentSubscription'])->name('notification.current.subscription');
        Route::post('/subscribe', [NotificationsController::class, 'subscribe'])->name('notification.subscribe');

        Route::prefix('affiliated')
            ->group(function () {
                Route::post('/characters', [NotificationsController::class, 'affiliatedCharacters'])->name('notification.affiliated.characters');
                Route::post('/corporations', [NotificationsController::class, 'affiliatedCorporations'])->name('notification.affiliated.corporations');
                Route::post('/alliances', [NotificationsController::class, 'affiliatedAlliances'])->name('notification.affiliated.alliances');
            });

    });

