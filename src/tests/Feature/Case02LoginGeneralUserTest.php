<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Faker\Factory;
class Case02LoginGeneralUserTest extends TestCase
{
    use DatabaseMigrations;
    protected function setUp(): void
    {
        parent::setUp();
        $faker = Factory::create('ja_JP');
        $name = $faker->name();
        $this->email = $faker->safeEmail();
        $this->password = $faker->unique->password(8);
        $this->diffPassword = $faker->unique->password(8);
        $this->post('/register', [
            'name' => $name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password,
        ]);
    }
    public function test_メールアドレスが未入力の場合、バリデーションメッセージが表示される()
    {
        $this->post('/login', [
            'email' => '',
            'password' => $this->password,
        ])
            ->assertSessionHasErrors([
                'email' => 'メールアドレスを入力してください',
            ]);
        $this->get('/login')->assertSeeInOrder([
            'email' => 'メールアドレスを入力してください',
        ]);
    }
    public function test_パスワードが未入力の場合、バリデーションメッセージが表示される()
    {
        $this->post('/login', [
            'email' => $this->email,
            'password' => '',
        ])
            ->assertSessionHasErrors([
                'password' => 'パスワードを入力してください',
            ]);
        $this->get('/login')->assertSeeInOrder([
            'password' => 'パスワードを入力してください',
        ]);
    }
    public function test_登録内容と一致しない場合、バリデーションメッセージが表示される()
    {
        $this->post('/login', [
            'email' => $this->email,
            'password' => $this->diffPassword,
        ])
            ->assertSessionHasErrors([
                'email' => 'ログイン情報が登録されていません',
            ]);
        $this->get('/login')->assertSeeInOrder([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }
}
