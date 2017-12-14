<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $time=date("Y-m-d H:i:s");
        view::share("ad",DB::table("ad")->where("time_start","<",$time)->where("time_end",">",$time)->where("status",1)->select("type","pic","position","url")->get());
        $log=DB::table("logo")->first();
        view::share("logo",$log);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
