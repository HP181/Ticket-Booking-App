<?php

namespace App\Providers;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use App\Services\LoggingService;
use App\Http\Middleware\AuthLogging;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register middleware using the router
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', AuthLogging::class);

        // Listen for auth events
        Event::listen(Login::class, function ($event) {
            LoggingService::logAuthEvent('user_logged_in', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
            ]);
        });

        Event::listen(Logout::class, function ($event) {
            LoggingService::logAuthEvent('user_logged_out', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
            ]);
        });

        Event::listen(Failed::class, function ($event) {
            LoggingService::logAuthEvent('login_failed', [
                'email' => $event->credentials['email'] ?? 'unknown',
            ]);
        });
    }
}