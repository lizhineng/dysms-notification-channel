<?php

namespace Zhineng\Notifications;

use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use Darabonba\OpenApi\Models\Config;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Zhineng\Notifications\Channels\DysmsChannel;

class DysmsServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/dysms.php', 'dysms');

        $this->app->singleton(Dysmsapi::class, function ($app) {
            return new Dysmsapi(new Config($app['config']['dysms']));
        });

        Notification::resolved(function (ChannelManager $service) {
            $service->extend('dysms', function ($app) {
                $config = $app['config']['dysms'];

                return new DysmsChannel($app->make(Dysmsapi::class), $config['signature']);
            });
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/dysms.php' => $this->app->configPath('dysms.php'),
            ], 'dysms');
        }
    }
}
