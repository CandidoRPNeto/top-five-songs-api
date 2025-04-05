<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;

    public function test_register_good_path(): void
    {
        $data = [
            "name" => "Test Good Path",
            "email" => "test@good.com",
            "password" => "Password@123",
            "password_confirmation" => "Password@123"
        ];
        $response = $this->post(route('auth.register'),$data);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success', 'message' => 'usuario criado com sucesso']);
    }

    public function test_register_with_none_information(): void
    {
        $data = [];
        $response = $this->postJson(route('auth.register'), $data);
        $response->assertStatus(422);
        $response->assertJson([
            "message" => "O nome é necessario. (and 2 more errors)",
            'errors' => [
                "name" => [
                    "O nome é necessario."
                ],
                "email" => [
                    "O email é necessario."
                ],
                "password" => [
                    "A senha é necessaria."
                ]
            ],
        ]);
    }

    public function test_register_with_password_invalid(): void
    {
        $data = [
            "name" => "Test Bad Path",
            "email" => "test@bad.com",
            "password" => "pa2s@",
            "password_confirmation" => "pa2s@",
        ];
        $response = $this->postJson(route('auth.register'), $data);
        $response->assertStatus(422);
        $response->assertJson([
            "message" => "A senha deve ter no mínimo 8 caracteres.",
        ]);
        $data['password'] = 'password';
        $data['password_confirmation'] = 'password';
        $response = $this->postJson(route('auth.register'), $data);
        $response->assertStatus(422);
        $response->assertJson([
            "message" => "A senha deve conter letras, números e pelo menos um caractere especial.",
        ]);
        $data['password'] = 'Password@123';
        $response = $this->postJson(route('auth.register'), $data);
        $response->assertStatus(422);
        $response->assertJson([
            "message" => "A confirmação da senha não corresponde.",
        ]);
    }

    public function test_register_with_email_invalid(): void
    {
        User::factory()->create(['email' => 'test@bad.com']);
        $data = [
            "name" => "Test Bad Path",
            "email" => "test@bad.com",
            "password" => "Password@123",
            "password_confirmation" => "Password@123"
        ];
        $response = $this->postJson(route('auth.register'), $data);
        $response->assertStatus(422);
        $response->assertJson([
            "message" => "Este e-mail já está em uso.",
        ]);
    }
}
