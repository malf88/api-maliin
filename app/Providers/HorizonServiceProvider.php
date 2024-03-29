<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
         Horizon::routeMailNotificationsTo(config('app.horizon_mail'));
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');

        // Horizon::night();

    }

    /**
     * Overload authorization method from \Laravel\Horizon\HorizonApplicationServiceProvider
     * to allow access to Horizon without having a logged in user.
     *
     * @return void
     */
    protected function authorization()
    {
        Horizon::auth(function ($request) {
            return true;
        });
    }
}
