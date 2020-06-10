<?php

namespace App\Providers;

use App\Model\Tracking;
use Illuminate\Support\ServiceProvider;

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
        view()->composer('detail', function($view){
            $fedex = Tracking::where('courier', '=', 'Fedex')->get();
            $yun = Tracking::where('courier', '=', 'Yun express')->count();
            $usps = Tracking::where('courier', '=', 'USPS')->count();
            $dhl = Tracking::where('courier', '=', 'DHL')->count();

            $view->with('fedex',$fedex);
            $view->with($yun);
        });
    }
}
