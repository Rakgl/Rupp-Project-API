<?php

namespace App\Providers;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class TelegramChannelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Notification::extend('telegram', function ($app) {
            return new class {
                public function send($notifiable, $notification)
                {
                    return $notification->toTelegram($notifiable);
                }
            };
        });
    }
}