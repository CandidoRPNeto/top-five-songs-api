<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions;


    protected function setUp(): void
    {
        parent::setUp();
        User::factory()->create(['email' => 'test@good.com']);
    }

    public function test_login_good_path(): void
    {
        $data = [
            'email' => 'test@good.com',
            'password' => 'password'
        ];
        $response = $this->post(route('auth.login'),$data);
        $response->assertStatus(200);
        $response->assertJson([ 'token_type' => 'bearer', ]);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'is_admin',
        ]);
    }

    public function test_login_wrong_email_path(): void
    {
        $data = [
            'email' => 'test@bad.com',
            'password' => 'password'
        ];
        $response = $this->post(route('auth.login'),$data);
        $response->assertStatus(401);
        $response->assertJson([ 'message' => 'email ou senha incorreto(s)' ]);
    }

    public function test_login_wrong_password_path(): void
    {
        $data = [
            'email' => 'test@good.com',
            'password' => 'password123'
        ];
        $response = $this->post(route('auth.login'),$data);
        $response->assertStatus(401);
        $response->assertJson([ 'message' => 'email ou senha incorreto(s)' ]);
    }

}
