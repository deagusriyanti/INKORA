<?php

namespace App\Providers;

use App\Models\CompanySetting;
use App\Services\FeatureGateService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Blade::if('feature', function (string $feature) {
            return FeatureGateService::allows($feature);
        });

        View::composer('*', function ($view) {
            $company = null;
            try {
                if (Schema::hasTable('company_settings')) {
                    $company = CompanySetting::first();
                }
            } catch (\Throwable $e) {
                $company = null;
            }
            $view->with('globalCompany', $company);
        });
    }
}