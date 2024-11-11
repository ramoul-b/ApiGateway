<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Role::class => \App\Policies\V1\RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('viewTelescope', function ($user) {
            // Retourne true si l'utilisateur est autorisé à voir Telescope
            // Exemple : return $user->isAdmin;
            return in_array($user->email, [
                // Liste des emails autorisés
            ]);
        });
    }
}
