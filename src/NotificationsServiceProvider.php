<?php

namespace Seatplus\Notifications;

use Illuminate\Support\ServiceProvider;
use Seatplus\Eveapi\Events\EveMailCreated;
use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Notifications\Listeners\EveMailListener;
use Seatplus\Notifications\Observers\EveMailObserver;

class NotificationsServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the JS & CSS,
        $this->addPublications();

        // Add routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/notifications.php');

        //Add Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');

        // Add event listeners
        $this->addEventListeners();

        // Add translations
        //$this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'web');
    }

    public function register()
    {
        $this->mergeConfigurations();
    }

    private function mergeConfigurations()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/package.sidebar.php',
            'package.sidebar'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../config/notification.channels.php',
            'notification.channel'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../config/notification.permissions.php',
            'web.permissions'
        );
    }

    private function addPublications()
    {
        /*
         * to publish assets one can run:
         * php artisan vendor:publish --tag=web --force
         * or use Laravel Mix to copy the folder to public repo of core.
         */
        $this->publishes([
            __DIR__ . '/../resources/js' => resource_path('js'),
        ], 'web');
    }

    private function addEventListeners()
    {
        Mail::observe(EveMailObserver::class);
    }
}
