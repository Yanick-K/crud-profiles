<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileEntityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up a valid user with Sanctum authentication.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testCreateProfileWithImage()
    {
        Sanctum::actingAs($this->user, ['*']);

        Storage::fake('public');

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'status' => 'active',
            'image' => \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg'),
        ];

        $response = $this->postJson('/api/profiles', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('profiles', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'status' => 'active',
        ]);
        Storage::disk('public')->assertExists('uploads/' . $data['image']->hashName());
    }

    public function testUpdateProfile()
    {
        Sanctum::actingAs($this->user, ['*']);

        $profile = Profile::factory()->create();

        $data = [
            'action' => 'update',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'status' => 'inactive',
        ];

        $response = $this->putJson("/api/profiles/{$profile->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseCount('profiles', 1);

        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'status' => 'inactive',
        ]);
    }

    public function testDeleteProfile()
    {
        Sanctum::actingAs($this->user, ['*']);

        $profile = Profile::factory()->create();

        $response = $this->deleteJson("/api/profiles/{$profile->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('profiles', Arr::except($profile->toArray(), ['image', 'created_at', 'updated_at']));

        // test delete in update endpoint method

        $profile = Profile::factory()->create();

        $response = $this->putJson("/api/profiles/{$profile->id}", ['action' => 'delete']);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('profiles', Arr::except($profile->toArray(), ['image', 'created_at', 'updated_at']));
    }

    public function testGetProfiles()
    {
        Sanctum::actingAs($this->user, ['*']);

        Profile::factory()->count(3)->create(['status' => 'active']);

        $response = $this->getJson('/api/profiles');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'first_name', 'last_name', 'status', 'image']
                ],
        ]);
    }

    public function testWithoutAuth()
    {
        $response = $this->getJson('/api/profiles');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'first_name', 'last_name', 'image']
            ],
        ]);

        $profile = Profile::factory()->create();

        $response = $this->deleteJson("/api/profiles/{$profile->id}");

        $response->assertStatus(401);
    }
}
