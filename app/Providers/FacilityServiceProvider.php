<?php

namespace App\Providers;
//use Illuminate\Contracts\View\View;

use App\Providers\Facility;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;


class FacilityServiceProvider extends ServiceProvider
{

public function register()
{
$this->app->singleton('Facility', function(){
return new Facility();
});

// Shortcut so developers don't need to add an Alias in app/config/app.php
$this->app->booting(function()
{
$loader = \Illuminate\Foundation\AliasLoader::getInstance();
$loader->alias('Facility', 'CLG\Facility\Facades\FacilityFacade');
});
}
}