<?php

namespace App\Providers;

use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Bridge\Sendgrid\Transport\SendgridTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Mail::extend('sendgrid+api', function (array $config = []) {
            $factory = new SendgridTransportFactory();

            return $factory->create(new Dsn(
                'sendgrid+api',
                'default',
                $config['key'] ?? null
            ));
        });
    }
}