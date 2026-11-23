<?php

namespace App\Providers;

use App\Models\AdminContent;
use App\Models\Penawaran;
use App\Policies\PenawaranPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\View;

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
        Gate::policy(Penawaran::class, PenawaranPolicy::class);

        ViewFacade::composer([
            'welcome',
            'pages.services.index',
            'pages.services.show',
            'pages.portfolios.index',
            'pages.portfolios.show',
            'pages.clients.index',
            'pages.contact',
            'pages.about',
        ], function (View $view): void {
            $hasManagedContentTable = Schema::hasTable('konten_admin');
            $managedContentTypes = $hasManagedContentTable
                ? AdminContent::query()->distinct()->pluck('jenis')->all()
                : [];
            $managedContent = $hasManagedContentTable
                ? AdminContent::query()
                    ->where('aktif', true)
                    ->where('status', 'tampil')
                    ->orderBy('urutan')
                    ->orderBy('judul')
                    ->get()
                    ->groupBy('jenis')
                : collect();

            $view->with([
                'managedContent' => $managedContent,
                'managedContentTypes' => $managedContentTypes,
            ]);
        });

        RateLimiter::for('login', function (Request $request): Limit {
            $email = Str::transliterate(Str::lower($request->string('email')->trim()->toString()));

            return Limit::perMinutes(
                (int) config('empirise.rate_limits.login.period_minutes'),
                (int) config('empirise.rate_limits.login.attempts'),
            )->by(hash('sha256', $email.'|'.$request->ip()));
        });

        RateLimiter::for('password-reset', function (Request $request): Limit {
            $email = Str::lower($request->string('email')->trim()->toString());

            return Limit::perMinutes(
                (int) config('empirise.rate_limits.password_reset.period_minutes'),
                (int) config('empirise.rate_limits.password_reset.attempts'),
            )->by(hash('sha256', $email.'|'.$request->ip()));
        });

        RateLimiter::for('register', fn (Request $request): Limit => Limit::perMinutes(
            (int) config('empirise.rate_limits.register.period_minutes'),
            (int) config('empirise.rate_limits.register.attempts'),
        )->by($request->ip()));

        RateLimiter::for('email-verification', fn (Request $request): Limit => Limit::perMinutes(
            (int) config('empirise.rate_limits.email_verification.period_minutes'),
            (int) config('empirise.rate_limits.email_verification.attempts'),
        )->by((string) $request->user()?->getAuthIdentifier()));

        RateLimiter::for('konsultasi', fn (Request $request): Limit => Limit::perMinutes(
            (int) config('empirise.rate_limits.consultation.period_minutes'),
            (int) config('empirise.rate_limits.consultation.attempts'),
        )->by($request->ip()));

        RateLimiter::for('consultation-replies', function (Request $request): Limit {
            return Limit::perMinutes(
                (int) config('empirise.rate_limits.consultation_replies.period_minutes'),
                (int) config('empirise.rate_limits.consultation_replies.attempts'),
            )->by((string) $request->user()?->getAuthIdentifier());
        });
    }
}
