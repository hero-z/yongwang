<?php

namespace WeixinPay;

use Illuminate\Support\ServiceProvider;
use WeixinPay\Lib\WxPayApi;
use WeixinPay\Lib\WxPayConfig;
use WeixinPay\Lib\WxPayDataBase;
use WeixinPay\Lib\WxPayException;
use WeixinPay\Lib\WxPayNotify;

class WeixinPayProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('wxpayapi',function(){
            return new WxPayApi();
        });

        $this->app->singleton('wxpayconfig',function(){
            return new WxPayConfig();
        });

        $this->app->singleton('wxpaydata',function(){
            return new WxPayDataBase();
        });

        $this->app->singleton('wxpayexcepition',function(){
            return new WxPayException();
        });

        $this->app->singleton('wxpaynotify',function(){
            return new WxPayNotify();
        });
    }
}
