<?php

namespace App\Providers;

use App\Models\Message;
use App\Models\TechnicianApplication;
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
        View::composer('layouts.partials.sidebar-nav', function ($view) {
            $user = auth()->user();

            $view->with(
                'unreadChatCount',
                $user ? Message::unreadCountForUser($user) : 0
            );

            $view->with(
                'pendingTechnicianApplications',
                $user?->isAdmin()
                    ? TechnicianApplication::where('status', TechnicianApplication::STATUS_PENDING)->count()
                    : 0
            );
        });
    }
}
