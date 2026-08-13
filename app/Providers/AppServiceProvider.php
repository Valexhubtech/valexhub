<?php

namespace App\Providers;

use App\Http\Middleware\AuthorizeDeploymentAccess;
use App\Http\Middleware\VerifyPaystackWebhookSignature;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use App\Mail\Transport\PlumeTransport;
use App\Services\Coolify\CoolifyDeploymentServiceContract;
use App\Services\Coolify\MockCoolifyDeploymentService;
use App\Services\Coolify\RealCoolifyDeploymentService;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CoolifyDeploymentServiceContract::class, function () {
            return config('services.coolify.use_real_service')
                ? app(RealCoolifyDeploymentService::class)
                : app(MockCoolifyDeploymentService::class);
        });
    }

    private function registerPlumeMailTransport(): void
    {
        Mail::extend('plume', function () {
            return new PlumeTransport(
                baseUrl: config('services.plume.base_url', ''),
                apiKey: config('services.plume.master_key', ''),
            );
        });

        // If MAIL_PROVIDER=plume, switch the default mailer at runtime
        if (config('services.mail_provider', 'resend') === 'plume') {
            config(['mail.default' => 'plume']);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment() == 'production') {
            $this->app['request']->server->set('HTTPS', true);
        }

        $this->setSchemaDefaultLength();
        $this->registerPlumeMailTransport();

        // Register activity log event listeners
        Event::listen(Login::class, LogSuccessfulLogin::class);
        Event::listen(Logout::class, LogSuccessfulLogout::class);

        Validator::extend('base64image', function ($attribute, $value, $parameters, $validator) {
            $explode = explode(',', $value);
            $allow = ['png', 'jpg', 'svg', 'jpeg'];
            $format = str_replace(
                [
                    'data:image/',
                    ';',
                    'base64',
                ],
                [
                    '', '', '',
                ],
                $explode[0]
            );

            // check file format
            if (! in_array($format, $allow)) {
                return false;
            }

            // check base64 format
            if (! preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                return false;
            }

            return true;
        });

        $this->bootRoute();

        $this->app->router->aliasMiddleware('paystack-webhook-signature', VerifyPaystackWebhookSignature::class);
        $this->app->router->aliasMiddleware('authorize-deployment-access', AuthorizeDeploymentAccess::class);
    }

    private function setSchemaDefaultLength(): void
    {
        try {
            Schema::defaultStringLength(191);
        } catch (Exception $exception) {
        }
    }

    public function bootRoute()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: (request()->header('CF-Connecting-IP') ?? request()->ip()));
        });

    }
}
