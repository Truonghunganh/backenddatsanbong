
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Contracts\View\View;

use Illuminate\Support\ServiceProvider;
use App\Setting;


class BaseController extends Controller
{

    protected $site_settings;

    public function __construct()
    {
        // Fetch the Site Settings object
        // $this->site_settings = Setting::all();
        // View::share('site_settings', $this->site_settings);
    }
}