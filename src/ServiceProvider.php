<?php

namespace Alexsoft\LaravelNotificationsTelegram;

use Illuminate\Notifications\ChannelManager;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app[ChannelManager::class]->extend('telegram', function ($app) {
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
        return [TelegramChannel::class];
    }
}
