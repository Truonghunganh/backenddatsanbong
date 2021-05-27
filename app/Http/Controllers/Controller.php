<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Contracts\View\View;

use Illuminate\Support\ServiceProvider;
use App\Setting;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    // public function __construct()
    // {
    //     // Fetch the Site Settings object
    //     // $site_settings = Setting::all();
    //     // View::share('site_settings', $site_settings);
    // }
    public function __construct()
    {
        // Fetch the Site Settings object
        // $site_settings = Setting::all();
        // View::share('site_settings', $site_settings);
    }

}
