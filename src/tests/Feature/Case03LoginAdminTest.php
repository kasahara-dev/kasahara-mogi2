<?php

namespace Tests\Feature;

use Database\Seeders\AdminsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Admin;
use Faker\Factory;

class Case03LoginAdminTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminsTableSeeder::class);
        $this->admin = Admin::first();
        $faker = Factory::create('ja_JP');
        $this->diffPassword = $faker->unique->password(8);
        while ($this->diffPassword == 'password') {
            $this->diffPassword = $faker->unique->password(8);
        }
    }
    public function test_メールアドレスが未入力の場合、バリデーションメッセージが表示される()
    {
        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => $this->admin->password,
        ]);
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
        $this->get('/admin/login')->assertSeeInOrder([
            'email' => 'メールアドレスを入力してください',
        ]);
    }
    public function test_パスワードが未入力の場合、バリデーションメッセージが表示される()
    {
        $response = $this->post('/admin/login', [
            'email' => $this->admin->email,
            'password' => '',
        ]);
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
        $this->get('/admin/login')->assertSeeInOrder([
            'password' => 'パスワードを入力してください',
        ]);
    }
    public function test_登録内容と一致しない場合、バリデーションメッセージが表示される()
    {
        $response = $this->post('/admin/login', [
            'email' => $this->admin->email,
            'password' => $this->diffPassword,
        ]);
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
        $this->get('/admin/login')->assertSeeInOrder([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }
}
