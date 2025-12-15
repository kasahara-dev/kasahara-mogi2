<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class Case01RegisterGeneralUserTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_名前が未入力の場合、バリデーションメッセージが表示される()
    {
        $faker = Factory::create('ja_JP');
        $response = $this->post('/register', [
            'name' => '',
            'email' => $faker->safeEmail(),
            'password' => $faker->password(8),
        ]);
        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);
        $this->get('/register')->assertSeeInOrder([
            'name' => 'お名前を入力してください',
        ]);
    }
    public function test_メールアドレスが未入力の場合、バリデーションメッセージが表示される()
    {
        $faker = Factory::create('ja_JP');
        $response = $this->post('/register', [
            'name' => $faker->name(),
            'email' => '',
            'password' => $faker->password(8),
        ]);
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
        $this->get('/register')->assertSeeInOrder([
            'email' => 'メールアドレスを入力してください',
        ]);
    }
    public function test_パスワードが8文字未満の場合、バリデーションメッセージが表示される()
    {
        $faker = Factory::create('ja_JP');
        $response = $this->post('/register', [
            'name' => $faker->name(),
            'email' => $faker->safeEmail(),
            'password' => $faker->password(7, 7),
        ]);
        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
        $this->get('/register')->assertSeeInOrder([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }
    public function test_パスワードが一致しない場合、バリデーションメッセージが表示される()
    {
        $faker = Factory::create('ja_JP');
        $response = $this->post('/register', [
            'name' => $faker->name(),
            'email' => $faker->safeEmail(),
            'password' => $faker->unique->password(8),
            'password_confirmation' => $faker->unique->password(8),
        ]);
        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません',
        ]);
        $this->get('/register')->assertSeeInOrder([
            'password' => 'パスワードと一致しません',
        ]);
    }
    public function test_パスワードが未入力の場合、バリデーションメッセージが表示される()
    {
        $faker = Factory::create('ja_JP');
        $response = $this->post('/register', [
            'name' => $faker->name(),
            'email' => $faker->safeEmail(),
            'password' => '',
        ]);
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
        $this->get('/register')->assertSeeInOrder([
            'password' => 'パスワードを入力してください',
        ]);
    }
    public function test_フォームに内容が入力されていた場合、データが正常に保存される()
    {
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
        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
        ]);
    }
}
