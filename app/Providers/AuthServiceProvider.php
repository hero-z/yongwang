<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**注册策略
     * The policy mappings for the application.
     *模型和处理访问控制的类以键值对的形式写到policies属性中
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        'App\Models\AlipayShopLists' => 'App\Policies\ShopLists',

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
       // 这里的$user是当前登录用户，laravel会处理,在调用的时候不用传入
          /*  Gate::define('show-post', function ($user, $post) {
            return $user->id == $post->user_id;
        });*/
    }
}
