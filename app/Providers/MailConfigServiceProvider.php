<?php

namespace App\Providers;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->extend('mail.manager', function (MailManager $manager) {
            $manager->extend('smtp', function ($config) {
                $transport = new EsmtpTransport(
                    $config['host'] ?? '127.0.0.1',
                    $config['port'] ?? 25,
                    $config['encryption'] ?? null
                );

                if (isset($config['username'])) {
                    $transport->setUsername($config['username']);
                }

                if (isset($config['password'])) {
                    $transport->setPassword($config['password']);
                }

                $streamOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ];

                $transport->setStreamOptions($streamOptions);

                return $transport;
            });

            return $manager;
        });
    }
}
