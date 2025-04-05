<?php

namespace Tests\Feature;

use App\Models\Songs;
use App\Models\User;
use App\UserRole;
use Database\Seeders\SongSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SongTest extends TestCase
{
    use DatabaseTransactions;

    private $admin;
    private $user;
    private $song;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['email' => 'test@adm.com', 'role' => UserRole::ADMIN->value]);
        $this->user = User::factory()->create(['email' => 'test@user.com']);
        $songSeeder = new SongSeeder();
        $songSeeder->userId = $this->user->id;
        $songSeeder->run();
        $this->song = Songs::first();
    }

    public function test_non_login_songs_actions(): void
    {
        $response = $this->deleteJson(route('songs.delete', ['song_id' => $this->song->id]));
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
        $response = $this->postJson(route('songs.store'),[]);
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
        $response = $this->putJson(route('songs.update', ['song_id' => $this->song->id]));
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
        $response = $this->getJson(route('songs.show', ['song_id' => $this->song->id]));
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_get_list_of_songs(): void
    {
        $response = $this->get(route('songs.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'user_id',
                    'title',
                    'views',
                    'youtube_id',
                    'thumb',
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
        $this->assertLessThanOrEqual(6, count($response->json('data')));
    }

    public function test_delete_songs(): void
    {
        $response = $this->actingAs($this->admin)->deleteJson(route('songs.delete', ['song_id' => $this->song->id]));
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success', 'message' => 'Musica apagada com sucesso']);
        $this->assertSoftDeleted('songs', [
            'id' => $this->song->id
        ]);
    }

    public function test_non_adm_delete_songs(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('songs.delete', ['song_id' => $this->song->id]));
        $response->assertStatus(403);
        $response->assertJson(['status' => 'fail', 'message' => 'Acesso não autorizado. Apenas administradores podem acessar este recurso.']);
    }

    public function test_get_song(): void
    {
        $response = $this->actingAs($this->admin)->getJson(route('songs.show', ['song_id' => $this->song->id]));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'song' => [
                "id",
                "user_id",
                "title",
                "views",
                "youtube_id",
                "thumb",
                "created_at",
                "updated_at",
                "deleted_at"
            ]
        ]);
    }

    public function test_non_adm_get_song(): void
    {
        $response = $this->actingAs($this->user)->get(route('songs.show', ['song_id' => $this->song->id]));
        $response->assertStatus(403);
        $response->assertJson(['status' => 'fail', 'message' => 'Acesso não autorizado. Apenas administradores podem acessar este recurso.']);
    }

    public function test_store_song(): void
    {
        $response = $this->actingAs($this->admin)->postJson(route('songs.store'),[
            'link' => 'https://www.youtube.com/watch?v=s9kVG2ZaTS4'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('songs', [
            'user_id' => $this->admin->id,
            'title' => 'O mineiro e o italiano',
            'youtube_id' => 's9kVG2ZaTS4',
            'thumb' => 'https://img.youtube.com/vi/s9kVG2ZaTS4/hqdefault.jpg'
        ]);
    }

    public function test_store_without_link_song(): void
    {
        $response = $this->actingAs($this->admin)->postJson(route('songs.store'),[]);
        $response->assertStatus(422);
        $response->assertJson([
            "message" => "O link é necessario.",
            "errors" => [
                "link" => [
                    "O link é necessario."
                ]
            ]
        ]);
    }

    public function test_non_adm_store_song(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('songs.store'), []);
        $response->assertStatus(403);
        $response->assertJson(['status' => 'fail', 'message' => 'Acesso não autorizado. Apenas administradores podem acessar este recurso.']);
    }

    public function test_update_song(): void
    {
        $data = [
            'title' => 'O mineiro',
            'youtube_id' => 'test'
        ];
        $response = $this->actingAs($this->admin)->putJson(route('songs.update', ['song_id' => $this->song->id]), $data);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success', 'message' => 'Musica atualizada com sucesso']);

        $this->assertDatabaseHas('songs', array_merge($data, [
            'id' => $this->song->id
        ]));
    }

    public function test_non_adm_update_song(): void
    {
        $response = $this->actingAs($this->user)->putJson(route('songs.update', ['song_id' => $this->song->id]), []);
        $response->assertStatus(403);
        $response->assertJson(['status' => 'fail', 'message' => 'Acesso não autorizado. Apenas administradores podem acessar este recurso.']);
    }
}
