<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Faker\Factory;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;

class Case16MailVerifyTest extends TestCase
{
    use DatabaseMigrations;
    public function test_会員登録後、認証メールが送信される()
    {
        Notification::fake();
        $faker = Factory::create('ja_JP');
        $name = $faker->name();
        $email = $faker->safeEmail();
        $password = $faker->unique->password(8);
        $response = $this->post('/register', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);
        $user = User::first();
        Notification::assertSentTo($user, VerifyEmail::class);
    }
    public function test_メール認証サイトのメール認証を完了すると、勤怠登録画面に遷移する()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );
        $response = $this->actingAs($user)->get($verificationUrl);
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/attendance');
    }
}
