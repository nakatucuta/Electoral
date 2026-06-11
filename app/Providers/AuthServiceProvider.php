<?php

namespace App\Providers;

use App\Models\Votante;
use App\Policies\VotantePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Votante::class => VotantePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user) {
            return $user->isAdmin() ? true : null;
        });
    }
}
