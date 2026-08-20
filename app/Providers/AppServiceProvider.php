<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\InvoiceSetting;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Use Bootstrap pagination
        Paginator::useBootstrapFive();

        // Share setting to all views
        View::composer('*', function ($view) {
            try {
                $setting = InvoiceSetting::getSetting();
            } catch (\Exception $e) {
                $setting = new InvoiceSetting([
                    'business_name' => 'Percetakan',
                    'primary_color' => '#0d6efd',
                    'dark_mode' => false,
                    'show_qr' => true,
                    'tax_enabled' => false,
                ]);
            }
            $view->with('setting', $setting);
        });
    }
}
