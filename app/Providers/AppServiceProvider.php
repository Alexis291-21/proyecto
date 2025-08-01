<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate;
use App\Models\Setting;
use App\Models\User;
use App\Policies\UserPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Compartimos la configuración global únicamente si la tabla 'settings' existe
     * y tenemos al menos un registro.
     */
    public function boot()
    {
        if (Schema::hasTable('settings')) {
            $settings = Setting::first();
            View::share('settings', $settings);
        }
    }
}

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * Asocia cada modelo con su política correspondiente.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        // Aquí puedes definir Gates adicionales si los necesitas:
        // Gate::define('update-settings', function (User $user) {
        //     return $user->isAdmin();
        // });
    }
}
