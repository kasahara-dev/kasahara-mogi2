<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('coachtech勤怠管理アプリ会員仮登録完了メール')
                ->greeting('こんにちは')
                ->line('ボタンをクリックして本登録を完了してください。')
                ->action('本登録', $url)
                ->line('このメールに心当たりのない場合は、このメールを破棄してください。')
                ->salutation('coachtech');
        });
    }
}
