<?php

namespace Jonreyg\LaravelRedisManager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class LaravelRedisManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerViews();
    }

    public function boot()
    {
        $this->publishResources();
        $this->registerRoutes();
    }

    public function publishResources()
    {
        if ($this->app->runningInConsole()) {

            $this->publishes([
              __DIR__.'/../config/config.php' => config_path('redis-manager.php'),
            ], 'config');
        
          }
    }

    public function registerConfig()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'redis-manager');
    }

    public function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'redis-manager');
    }

    public function registerRoutes()
    {
        Route::group($this->webRouteConfiguration(), function() {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
        
        Route::group($this->apiRouteConfiguration(), function() {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }

    protected function webRouteConfiguration()
    {
        return [
            'prefix' => 'redis-manager',
            'middleware' => array_merge(config('redis-manager.web_route_middleware', ['web']), ['web']),
        ];
    }

    protected function apiRouteConfiguration()
    {
        return [
            'prefix' => 'api/redis-manager',
            'middleware' => array_merge(config('redis-manager.api_route_middleware', ['api']), ['api']),
        ];
    }
}