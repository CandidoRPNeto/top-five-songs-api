<?php

namespace Tests\Feature;

use App\Models\Request;
use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RequestTest extends TestCase
{
    use DatabaseTransactions;

    private $admin;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['email' => 'test@adm.com', 'role' => UserRole::ADMIN->value]);
        $this->user = User::factory()->create(['email' => 'test@user.com']);
    }

    public function test_non_login_request_actions(): void
    {
        $response = $this->postJson(route('request.send'),[]);
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
        $response = $this->getJson(route('request.index'),[]);
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
        $response = $this->patchJson(route('request.accept', ['request_id' => 'test']));
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
        $response = $this->patchJson(route('request.refuse', ['request_id' => 'test']));
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_get_list_of_request(): void
    {
        $response = $this->actingAs($this->admin)->get(route('request.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'admin_id',
                    'approved',
                    'link',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ]
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ]);
    }

    public function test_non_adm_get_list_of_request(): void
    {
        $response = $this->actingAs($this->user)->get(route('request.index'));
        $response->assertStatus(403);
        $response->assertJson([ "message" => "Acesso não autorizado. Apenas administradores podem acessar este recurso." ]);
    }

    public function test_send_request(): void
    {
        $link = 'https://www.youtube.com/watch?v=s9kVG2ZaTS4';
        $response = $this->actingAs($this->user)->post(route('request.send'),[
            'link' => $link
        ]);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'request enviada com sucesso']);
        $this->assertDatabaseHas('requests', [
            'link' => $link,
            'user_id' => $this->user->id
        ]);
    }

    public function test_send_non_youtube_request(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('request.send'),[
            'link' => 'https://open.spotify.com/intl-pt/track/6kOE4yl1jXqA2y48EPCt7j?si=db311144e93c4139'
        ]);
        $response->assertStatus(422);
        $response->assertJson([
            "message" => "O link deve ser uma URL válida do YouTube.",
            "errors" => [
                "link" => [
                    "O link deve ser uma URL válida do YouTube."
                ]
            ]
        ]);
    }

    public function test_send_without_link_request(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('request.send'),[]);
        $response->assertStatus(422);
        $response->assertJson([
            "message" => "O link é necessario."
        ]);
    }

    public function test_refuse_request(): void
    {
        $link = 'https://www.youtube.com/watch?v=s9kVG2ZaTS4';
        $request = Request::create([
            'user_id' => $this->user->id,
            'link' => $link
        ]);
        $response = $this->actingAs($this->admin)->patchJson(route('request.refuse', $request->id),[]);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'request recusada com sucesso']);
        $this->assertTrue(
            Request::withTrashed()
                ->where('link', $link)
                ->where('user_id', $this->user->id)
                ->where('admin_id', $this->admin->id)
                ->where('approved', false)
                ->whereNotNull('deleted_at')
                ->exists()
        );
    }

    public function test_accept_request(): void
    {
        $link = 'https://www.youtube.com/watch?v=s9kVG2ZaTS4';
        $request = Request::create([
            'user_id' => $this->user->id,
            'link' => $link
        ]);
        $response = $this->actingAs($this->admin)->patchJson(route('request.accept', $request->id),[]);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'request aprovada com sucesso']);
        $this->assertTrue(
            Request::withTrashed()
                ->where('link', $link)
                ->where('user_id', $this->user->id)
                ->where('admin_id', $this->admin->id)
                ->where('approved', true)
                ->exists()
        );
        $this->assertDatabaseHas('songs', [
            'user_id' => $this->user->id,
            'title' => 'O mineiro e o italiano',
            'youtube_id' => 's9kVG2ZaTS4',
            'thumb' => 'https://img.youtube.com/vi/s9kVG2ZaTS4/hqdefault.jpg'
        ]);
    }

    public function test_non_adm_refuse_request(): void
    {
        $request = Request::create([
            'user_id' => $this->user->id,
            'link' => 'https://www.youtube.com/watch?v=s9kVG2ZaTS4'
        ]);
        $response = $this->actingAs($this->user)->patchJson(route('request.refuse', $request->id),[]);
        $response->assertStatus(403);
        $response->assertJson([ "message" => "Acesso não autorizado. Apenas administradores podem acessar este recurso." ]);
    }

    public function test_non_adm_accept_request(): void
    {
        $request = Request::create([
            'user_id' => $this->user->id,
            'link' => 'https://www.youtube.com/watch?v=s9kVG2ZaTS4'
        ]);
        $response = $this->actingAs($this->user)->patchJson(route('request.accept', $request->id),[]);
        $response->assertStatus(403);
        $response->assertJson([ "message" => "Acesso não autorizado. Apenas administradores podem acessar este recurso." ]);
    }
}
