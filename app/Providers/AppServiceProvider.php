<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Timezone para México / Tijuana
        config(['app.timezone' => env('APP_TIMEZONE', 'America/Tijuana')]);
        Carbon::setLocale('es_MX');

        // Strict model in development: catch lazy loading, missing fillable, etc.
        Model::shouldBeStrict(! app()->isProduction());

        // Prevent N+1 in production
        Model::preventLazyLoading(! app()->isProduction());
    }
}
