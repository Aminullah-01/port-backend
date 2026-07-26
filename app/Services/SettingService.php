<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function __construct(private FileUploadService $fileUploadService) {}

    public function getSettings(): Setting
    {
        return Setting::firstOrCreate(['id' => 1], [
            'site_name' => 'Aminu Gambo Abubakar — Portfolio',
            'hero_title' => "Hi, I'm Aminu",
            'hero_subtitle' => 'Computer Science student crafting premium digital experiences.',
            'seo_title' => 'Aminu Gambo Abubakar — Full Stack Developer',
            'seo_description' => 'Portfolio of Aminu Gambo Abubakar, Software Engineer and Full Stack Developer.',
            'social_links' => [
                'github' => 'https://github.com/Aminullah-01',
                'linkedin' => 'https://linkedin.com/in/aminu-gambo-abubakar-073139340',
                'twitter' => 'https://x.com/Aminullah7360',
            ],
        ]);
    }

    public function updateSettings(array $data): Setting
    {
        $setting = $this->getSettings();

        if (isset($data['logo'])) {
            $data['logo'] = $this->fileUploadService->uploadFile($data['logo'], 'settings', $setting->logo);
        }

        if (isset($data['favicon'])) {
            $data['favicon'] = $this->fileUploadService->uploadFile($data['favicon'], 'settings', $setting->favicon);
        }

        $setting->update($data);

        return $setting->fresh();
    }
}
