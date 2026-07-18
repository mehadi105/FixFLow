<?php

namespace App\Providers;

use App\Models\Message;
use App\Models\TechnicianApplication;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        $this->configureRateLimiting();

        View::composer(['layouts.partials.sidebar-nav', 'layouts.partials.top-navbar', 'messages.index', 'layouts.app'], function ($view) {
            $user = auth()->user();

            $unreadChatCount = $user ? Message::unreadCountForUser($user) : 0;
            $pendingTechnicianApplications = $user?->isAdmin()
                ? TechnicianApplication::where('status', TechnicianApplication::STATUS_PENDING)->count()
                : 0;

            $view->with('unreadChatCount', $unreadChatCount);
            $view->with('pendingTechnicianApplications', $pendingTechnicianApplications);
            $view->with('notificationTotal', $unreadChatCount + $pendingTechnicianApplications);
        });
    }

    /**
     * Named limits keep authentication and AJAX traffic predictable.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(10)->by(
            Str::transliterate(Str::lower((string) $request->input('email')).'|'.$request->ip())
        ));

        RateLimiter::for('password-reset', fn (Request $request) => Limit::perMinute(5)->by(
            Str::transliterate(Str::lower((string) $request->input('email')).'|'.$request->ip())
        ));

        RateLimiter::for('registration', fn (Request $request) => Limit::perHour(5)->by($request->ip()));

        RateLimiter::for('ajax', fn (Request $request) => Limit::perMinute(120)->by(
            (string) ($request->user()?->id ?? $request->ip())
        ));

        RateLimiter::for('messages', fn (Request $request) => Limit::perMinute(30)->by(
            (string) ($request->user()?->id ?? $request->ip())
        ));
    }
}
