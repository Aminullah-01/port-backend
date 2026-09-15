<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Models\AnalyticsEvent;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profileService) {}

    public function show(): JsonResponse
    {
        $profile = $this->profileService->getProfile();

        return response()->json([
            'success' => true,
            'message' => 'Profile retrieved successfully',
            'data' => new ProfileResource($profile),
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $profile = $this->profileService->getProfile();
        $updated = $this->profileService->updateProfile($profile, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => new ProfileResource($updated),
        ]);
    }

    public function downloadResume(): Response|RedirectResponse
    {
        $profile = $this->profileService->getProfile();

        // Track CV download in analytics
        try {
            AnalyticsEvent::create([
                'event_type' => 'cv_download',
                'page_url' => request()->header('referer') ?: '/resume',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        if ($profile->resume_data) {
            $content = base64_decode($profile->resume_data);
            $filename = $profile->resume_filename ?: ($profile->full_name ? str_replace(' ', '_', $profile->full_name) . '_Resume' : 'Resume');

            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            if (!$extension) {
                $ext = match ($profile->resume_mime) {
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                    'application/msword' => 'doc',
                    default => 'pdf',
                };
                $filename .= '.' . $ext;
            }

            $mime = $profile->resume_mime ?: 'application/pdf';

            return response($content, 200, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'attachment; filename="' . addslashes($filename) . '"',
                'Content-Length' => strlen($content),
                'Cache-Control' => 'no-cache, private',
            ]);
        }

        if ($profile->resume_url && filter_var($profile->resume_url, FILTER_VALIDATE_URL)) {
            return redirect()->away($profile->resume_url);
        }

        abort(404, 'Resume not found');
    }

    public function viewResume(): Response|RedirectResponse
    {
        $profile = $this->profileService->getProfile();

        if ($profile->resume_data) {
            $content = base64_decode($profile->resume_data);
            $filename = $profile->resume_filename ?: 'Resume.pdf';
            $mime = $profile->resume_mime ?: 'application/pdf';

            return response($content, 200, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . addslashes($filename) . '"',
                'Content-Length' => strlen($content),
                'Cache-Control' => 'no-cache, private',
            ]);
        }

        if ($profile->resume_url && filter_var($profile->resume_url, FILTER_VALIDATE_URL)) {
            return redirect()->away($profile->resume_url);
        }

        abort(404, 'Resume not found');
    }
}
