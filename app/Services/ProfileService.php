<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Certificate;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use Illuminate\Support\Facades\DB;

class ProfileService
{
    public function __construct(private FileUploadService $fileUploadService) {}

    public function getProfile(): Profile
    {
        $profile = Profile::with('user')->first();

        if (!$profile) {
            $profile = Profile::create([
                'first_name' => 'Aminu',
                'last_name' => 'Gambo Abubakar',
                'email' => 'aminugamboabubakar33@gmail.com',
                'headline' => 'Frontend & Backend Developer',
                'bio' => 'Computer Science student crafting premium digital experiences.',
                'phone' => '+234 913 264 7360',
                'location' => 'Gombe, Nigeria',
            ]);
        }

        $profile->setRelation('projects', Project::where('status', 'published')->orderBy('display_order')->get());
        $profile->setRelation('skills', Skill::orderBy('display_order')->get());
        $profile->setRelation('services', Service::orderBy('display_order')->get());
        $profile->setRelation('certificates', Certificate::orderBy('display_order')->get());
        $profile->setRelation('blogs', BlogPost::where('status', 'published')->orderBy('created_at', 'desc')->get());

        return $profile;
    }

    public function updateProfile(Profile $profile, array $data): Profile
    {
        return DB::transaction(function () use ($profile, $data) {
            if (isset($data['avatar'])) {
                $data['avatar'] = $this->fileUploadService->uploadFile($data['avatar'], 'avatars', $profile->avatar);
            }

            if (isset($data['resume'])) {
                $data['resume_url'] = $this->fileUploadService->uploadFile($data['resume'], 'resumes', $profile->resume_url);
                unset($data['resume']);
            } elseif (isset($data['resume_url'])) {
                $data['resume_url'] = $this->fileUploadService->uploadFile($data['resume_url'], 'resumes', $profile->resume_url);
            }

            $profile->update($data);

            if ($profile->user_id && (isset($data['first_name']) || isset($data['email']))) {
                $profile->user->update([
                    'name' => $profile->full_name,
                    'email' => $profile->email,
                ]);
            }

            return $this->getProfile();
        });
    }
}
