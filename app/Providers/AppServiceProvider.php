<?php

namespace App\Providers;

use App\Services\MenuService;
use App\Services\SettingService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share sidebar menus
        View::composer('partials.sidebar', function ($view): void {
            $view->with(
                'sidebarMenus',
                app(MenuService::class)->getSidebarMenus(),
            );
        });

        // Share application settings with sidebar and layout
        View::composer(['partials.sidebar', 'layouts.app'], function ($view): void {
            $view->with(
                'appSettings',
                app(SettingService::class)->all(),
            );
        });
    }
}
