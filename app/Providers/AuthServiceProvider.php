<?php

namespace App\Providers;

use App\Advertise;
use App\WorkGroup;
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\ClientDetail' => 'App\Policies\ClientDetailPolicy',
        'App\AdInviter' => 'App\Policies\AdInviterPolicy',
        'App\WorkGroup' => 'App\Policies\WorkGroupPolicy',
        'App\Subscription' => 'App\Policies\SubscriptionPolicy',
        'App\User' => 'App\Policies\UserPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // Create a gate that user can see advertise or not
        Gate::define('client-can-see-advertise', function ($user, $advertise) {
            if (Carbon::now()->greaterThanOrEqualTo(Carbon::parse($advertise->free_date))) {
                return true;
            }
            if (auth()->user() == null) {
                return false;
            }
            if ($user->detail == null) {
                return false;
            }

            $userWorkGroups = !$user->detail->workGroups->isEmpty() ? $user->detail->workGroups->where('parent_id', '!=', null)->pluck('id') : null;
            // check client workGroup is exists in
            if ($userWorkGroups == null) {
                return false;
            } else {
                $isExists = false;
                $userWorkGroups->map(function ($id) use ($advertise , &$isExists) {
                    if (in_array($id, $advertise->workGroups->pluck('id')->toArray())) {
                        $isExists = true;
                    }
                });

                return $isExists;
            }
        });

        Gate::define('has-plane', function ($user) {
            if ($user->detail == null) {
                return false;
            } else {
                $clientDetail = $user->detail;
                if ($clientDetail->subscription_date == null || $clientDetail->subscription_title == null) {
                    return false;
                } else {
                    if (Carbon::parse($clientDetail->subscription_date) < Carbon::now()) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        });

        Gate::define('has-plane-in-admin', function ($user, $clientDetail) {
            if ($clientDetail == null) {
                return false;
            } else {
                if ($clientDetail->subscription_date == null || $clientDetail->subscription_title == null) {
                    return false;
                } else {
                    if (Carbon::parse($clientDetail->subscription_date) < Carbon::now()) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        });

        Gate::define('not-choosed-work-groups', function ($user) {
            if ($user->detail == null) {
                return false;
            } else {
                $clientDetail = $user->detail;
                if (count($clientDetail->workGroups->where('parent_id', '!=', null)) == 0 && Gate::allows('has-plane')) {
                    return true;
                } else {
                    return false;
                }
            }
        });

        Gate::define('in-work-groups', function ($user, $advertise) {
            if ($user->detail == null) {
                return false;
            } else {
                $clientDetail = $user->detail;
                $clientWorkGroups = $clientDetail->workGroups->where('parent_id', '!=', null);
                if (count($clientWorkGroups) != 0 && Gate::allows('has-plane')) {
                    $clientWorkGroupsId = $clientWorkGroups->pluck('id');
                    $advertiseWorkGroupsId = $advertise->workGroups->pluck('id')->toArray();
                    foreach ($clientWorkGroupsId as $clientWorkGroupId) {
                        if (in_array($clientWorkGroupId, $advertiseWorkGroupsId)) {
                            return true;
                        }
                    }

                    return false;
                } else {
                    return false;
                }
            }
        });

        $this->registerPolicies();
    }
}
