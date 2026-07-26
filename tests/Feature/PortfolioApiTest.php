<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic database requirements
        $this->seed();
    }

    public function test_can_fetch_public_profile(): void
    {
        $response = $this->getJson('/api/v1/profile');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'aminugamboabubakar33@gmail.com');
    }

    public function test_can_fetch_public_projects(): void
    {
        $response = $this->getJson('/api/v1/projects');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_fetch_public_skills(): void
    {
        $response = $this->getJson('/api/v1/skills');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_can_fetch_public_services(): void
    {
        $response = $this->getJson('/api/v1/services');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_can_submit_contact_message(): void
    {
        $response = $this->postJson('/api/v1/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Project Inquiry',
            'message' => 'Hello, I would like to work with you on a software project.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'john@example.com',
            'subject' => 'Project Inquiry',
        ]);
    }

    public function test_admin_can_login_and_access_dashboard(): void
    {
        $loginResponse = $this->postJson('/api/v1/login', [
            'email' => 'aminugamboabubakar33@gmail.com',
            'password' => 'password',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $token = $loginResponse->json('data.token');

        $dashResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/dashboard');

        $dashResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total_projects', 3);
    }

    public function test_admin_can_create_and_delete_project(): void
    {
        $user = User::first();
        $token = $user->createToken('test-token')->plainTextToken;

        $createResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/projects', [
                'title' => 'New SaaS Platform',
                'description' => 'A brand new SaaS application.',
                'category' => 'Web App',
                'technologies' => ['Laravel', 'Vue.js'],
                'featured' => true,
                'status' => 'published',
            ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.title', 'New SaaS Platform');

        $projectId = $createResponse->json('data.id');

        $deleteResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/v1/projects/{$projectId}");

        $deleteResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertSoftDeleted('projects', ['id' => $projectId]);
    }
}
