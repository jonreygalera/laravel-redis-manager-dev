<?php

namespace Jonreyg\LaravelRedisManager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
use Jonreyg\LaravelRedisManager\Http\Middleware\ForceJsonResponse;

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
        $this->registerMiddleware();
    }

    public function publishResources()
    {
        if ($this->app->runningInConsole()) {

            $this->publishes([
              __DIR__.'/../config/config.php' => config_path('redis-manager.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('redis-manager'),
              ], 'assets');
        
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

    public function registerMiddleware()
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('forceJsonResponse', ForceJsonResponse::class);
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
            'middleware' => array_merge(config('redis-manager.api_route_middleware', ['api', 'forceJsonResponse']), ['api', 'forceJsonResponse']),
        ];
    }
}