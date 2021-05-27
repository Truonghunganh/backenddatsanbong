<?php

namespace App\Providers;

//use Illuminate\Contracts\View\View;

use App\Settings;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $checkdatsan=true;
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Settings::class, function () {
            return Settings::make(storage_path('app/settings.json'));
        });
    }

    // public function register()
    // {
    //     //
    // }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::share('key', true);
        // view()->composer('*', function (View $view) {
        //     $site_settings = Setting::all();
        //     $view->with('site_settings', $site_settings);
        // });
        
    }
}

