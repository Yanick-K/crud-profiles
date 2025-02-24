<?php

namespace Tests\Feature;

use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        // Création d'un utilisateur
        $this->profile = Profile::factory()->create();

        // Authentification via Sanctum
        Sanctum::actingAs($this->profile->user, ['*']);
    }

    /**
     * Test creating a profile with an image file.
     */
    public function testCreateProfileWithImage()
    {
        Storage::fake('public'); // Utiliser un fake pour les fichiers

        $data = [
            'firstName' => 'John',
            'lastName' => 'Doe',
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
        Storage::disk('public')->assertExists('avatars/' . $data['image']->hashName());
    }

    /**
     * Test updating a profile.
     */
    public function testUpdateProfile()
    {
        $profile = Profile::factory()->create();

        $data = [
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'status' => 'inactive',
        ];

        $response = $this->putJson("/api/profiles/{$profile->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('profiles', [
            'id' => $profile->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'status' => 'inactive',
        ]);
    }

    /**
     * Test deleting a profile.
     */
    public function testDeleteProfile()
    {
        $profile = Profile::factory()->create();

        $response = $this->deleteJson("/api/profiles/{$profile->id}");

        $response->assertStatus(200);
        $this->assertDeleted($profile);
    }

    /**
     * Test retrieving profiles.
     */
    public function testGetProfiles()
    {
        $response = $this->getJson('/api/profiles');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'firstName', 'lastName', 'status', 'image'],
        ]);
    }

    /**
     * Test authentication for Sanctum-protected endpoints.
     */
    public function testAuthenticatedProfileOnly()
    {
        // Authentification
        $response = $this->postJson('/api/profiles', []);
        $response->assertStatus(401);

        // Authentification correcte
        Sanctum::actingAs($this->profile->user, ['*']);
        $response = $this->postJson('/api/profiles', []);
        $response->assertStatus(201);
    }
}
