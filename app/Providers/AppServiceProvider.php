<?php

namespace App\Providers;

use App\Services\MailService;
use App\Services\ViolationNotificationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register MailService as singleton
        $this->app->singleton(MailService::class, function ($app) {
            return new MailService();
        });

        // Register ViolationNotificationService
        $this->app->singleton(ViolationNotificationService::class, function ($app) {
            return new ViolationNotificationService($app->make(MailService::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
