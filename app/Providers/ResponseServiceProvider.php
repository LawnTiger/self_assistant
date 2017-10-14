<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Response\JsonResponse;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('jResponse', function ($app) {
            return new JsonResponse;
        });
    }
}
