<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model→policy mappings.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // 1) Don’t register our dynamic gate when running in artisan
        if ($this->app->runningInConsole()) {
            return;
        }

        // 2) Defer until after the application has fully booted
        $this->app->booted(function () {
            try {
                // 3) Only proceed if the permissions table really exists
                if (! Schema::hasTable('permissions')) {
                    return;
                }
            } catch (Throwable $e) {
                // something’s wrong with the DB – skip registering gates
                return;
            }

            // 4) Register your catch-all “before” hook
            Gate::before(function ($user, $ability, $arguments) {
                // $ability is the module slug (e.g. "orders")
                // $arguments[0] is the action (e.g. "create", "view")
                $action = $arguments[0] ?? null;

                return $user->hasPermission($user->roles, $ability, $action);
            });
        });
    }
}
