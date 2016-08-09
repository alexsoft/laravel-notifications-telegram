<?php

namespace Alexsoft\LaravelNotificationsTelegram;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        \Notification::extend('telegram', function($app) {
            return new TelegramChannel(new \GuzzleHttp\Client, $app['config']['services.telegram-notifications-bot-token.key']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [TelegramChannel::class, TelegramMessage::class];
    }
}