<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Profile;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateIssuesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::first();
        $this->token = $this->admin->createToken('test-token')->plainTextToken;
    }

    /** Issue 1: Test Project Update via POST _method=PUT and PUT methods */
    public function test_can_update_project_with_method_spoofing(): void
    {
        $project = Project::first();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson("/api/v1/projects/{$project->id}", [
                'title' => 'Updated Project Title',
                'description' => 'Updated Description',
                'category' => 'Web App',
                '_method' => 'PUT',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Updated Project Title');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Updated Project Title',
        ]);
    }

    public function test_can_update_project_via_direct_put(): void
    {
        $project = Project::first();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/v1/projects/{$project->id}", [
                'title' => 'Direct PUT Title',
                'description' => 'Direct PUT Description',
                'category' => 'Web App',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Direct PUT Title');
    }

    /** Issue 2: Test Certificate Update via POST _method=PUT and PUT methods */
    public function test_can_update_certificate_with_method_spoofing(): void
    {
        $cert = Certificate::first();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson("/api/v1/certificates/{$cert->id}", [
                'title' => 'Updated Cert Title',
                'organization' => 'Meta',
                '_method' => 'PUT',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Updated Cert Title');
    }

    /** Issue 3 & 4: Test Profile and Resume Upload via POST _method=PUT and PUT methods */
    public function test_can_update_profile_and_upload_resume_with_method_spoofing(): void
    {
        Storage::fake('public');

        $resumeFile = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/v1/profile', [
                'first_name' => 'Aminu Updated',
                'last_name' => 'Abubakar',
                'headline' => 'Senior Full Stack Engineer',
                'resume' => $resumeFile,
                '_method' => 'PUT',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.first_name', 'Aminu Updated');

        $profile = Profile::first();
        $this->assertNotNull($profile->resume_url);
    }

    public function test_can_update_profile_via_direct_put(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson('/api/v1/profile', [
                'first_name' => 'Aminu Direct PUT',
                'headline' => 'Senior Backend Engineer',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.first_name', 'Aminu Direct PUT');
    }
}
