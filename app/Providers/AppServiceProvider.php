<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
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
        ResetPassword::toMailUsing(function ($user, $token) {
            $link = route('password.reset', ['token' => $token, 'email' => $user->email]);

            return (new MailMessage)
                ->subject('Reset Password Akun Media Creative')
                ->line('Kami menerima permintaan reset password untuk akun Anda.')
                ->action('Reset Password', $link)
                ->line('Tautan ini berlaku selama 60 menit. Abaikan email ini jika Anda tidak memintanya.');
        });
    }
}
