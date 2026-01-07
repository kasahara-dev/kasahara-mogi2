<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;

class Case16MailVerifyTest extends DuskTestCase
{
    use DatabaseMigrations;
    public function test_メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する()
    {
        $faker = Factory::create('ja_JP');
        $email = $faker->safeEmail();
        $password = $faker->unique->password;
        $this->user = User::create([
            'name' => $faker->name(),
            'email' => $email,
            'password' => bcrypt($password),
        ]);
        $this->browse(function (Browser $browser) {
            $browser
                ->loginAs($this->user)
                ->visit('http://mailhog:8025')
                ->assertSee('MailHog');
        });
    }
}
