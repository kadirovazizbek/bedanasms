<?php

namespace BedanaSmsSender\Integrations;

use BedanaSmsSender\SmsClient;

/**
 * Laravel Service Provider for PHP SMS Sender
 */
class LaravelServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/sms-sender.php', 'sms-sender');

        $this->app->singleton(SmsClient::class, function ($app) {
            $config = $app['config']['sms-sender'];
            return new SmsClient(
                $config['api_key'],
                $config['base_url']
            );
        });

        $this->app->alias(SmsClient::class, 'sms-client');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/sms-sender.php' => config_path('sms-sender.php'),
        ], 'config');
    }
}
