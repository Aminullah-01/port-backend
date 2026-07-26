<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Profile;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin User
        $user = User::updateOrCreate(
            ['email' => 'aminugamboabubakar33@gmail.com'],
            [
                'name' => 'Aminu Gambo Abubakar',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Create Profile
        Profile::updateOrCreate(
            ['email' => 'aminugamboabubakar33@gmail.com'],
            [
                'user_id' => $user->id,
                'first_name' => 'Aminu',
                'last_name' => 'Gambo Abubakar',
                'headline' => 'Frontend & Backend Developer',
                'bio' => 'I design and build modern, accessible, and delightful web products. My work spans frontend and backend engineering, and applied AI — bridging craft, code, and creativity.',
                'phone' => '+234 913 264 7360',
                'location' => 'Gombe, Nigeria',
                'avatar' => 'https://api.dicebear.com/9.x/avataaars/svg?seed=Aminu&backgroundColor=b6e3f4',
                'resume_url' => null,
                'website' => 'https://github.com/Aminullah-01',
                'github_url' => 'https://github.com/Aminullah-01',
                'linkedin_url' => 'https://linkedin.com/in/aminu-gambo-abubakar-073139340',
                'facebook_url' => 'https://www.facebook.com/ameenu.gabubakar.7/',
                'twitter_url' => 'https://x.com/Aminullah7360',
                'whatsapp_url' => 'https://wa.me/2349132647360',
            ]
        );

        // 3. Create Settings
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'site_name' => 'Aminu Gambo Abubakar — Portfolio',
                'hero_title' => "Hi, I'm Aminu",
                'hero_subtitle' => 'Computer Science student crafting premium digital experiences.',
                'theme_colors' => [
                    'primary' => '#6366f1',
                    'background' => '#0f172a',
                ],
                'seo_title' => 'Aminu Gambo Abubakar — Senior Full Stack Engineer',
                'seo_description' => 'Portfolio of Aminu Gambo Abubakar, Software Engineer and Full Stack Developer.',
                'social_links' => [
                    'github' => 'https://github.com/Aminullah-01',
                    'linkedin' => 'https://linkedin.com/in/aminu-gambo-abubakar-073139340',
                    'twitter' => 'https://x.com/Aminullah7360',
                    'facebook' => 'https://www.facebook.com/ameenu.gabubakar.7/',
                    'whatsapp' => 'https://wa.me/2349132647360',
                ],
            ]
        );

        // 4. Create Skills
        $skillsData = [
            ['name' => 'React', 'category' => 'Frontend', 'percentage' => 95, 'color' => '#61dafb', 'display_order' => 1],
            ['name' => 'TypeScript', 'category' => 'Frontend', 'percentage' => 90, 'color' => '#3178c6', 'display_order' => 2],
            ['name' => 'Next.js', 'category' => 'Frontend', 'percentage' => 88, 'color' => '#000000', 'display_order' => 3],
            ['name' => 'Tailwind CSS', 'category' => 'Frontend', 'percentage' => 96, 'color' => '#06b6d4', 'display_order' => 4],
            ['name' => 'JavaScript', 'category' => 'Programming', 'percentage' => 94, 'color' => '#f7df1e', 'display_order' => 5],
            ['name' => 'Laravel / PHP', 'category' => 'Backend', 'percentage' => 92, 'color' => '#ff2d20', 'display_order' => 6],
            ['name' => 'PostgreSQL', 'category' => 'Database', 'percentage' => 88, 'color' => '#4169e1', 'display_order' => 7],
            ['name' => 'Git', 'category' => 'Developer Tools', 'percentage' => 90, 'color' => '#f05032', 'display_order' => 8],
            ['name' => 'VS Code', 'category' => 'Developer Tools', 'percentage' => 95, 'color' => '#007acc', 'display_order' => 9],
        ];

        foreach ($skillsData as $skill) {
            Skill::updateOrCreate(['name' => $skill['name']], $skill);
        }

        // 5. Create Services
        $servicesData = [
            ['title' => 'Frontend Development', 'icon' => 'code2', 'description' => 'Production-ready React & TypeScript apps.', 'features' => ['Component architecture', 'Performance tuning', 'Accessibility', 'Testing'], 'display_order' => 1],
            ['title' => 'Responsive Web Design', 'icon' => 'layers', 'description' => 'Mobile-first sites that feel great everywhere.', 'features' => ['Fluid layouts', 'Design tokens', 'Cross-browser', 'Motion'], 'display_order' => 2],
            ['title' => 'UI/UX Design', 'icon' => 'palette', 'description' => 'End-to-end product design in Figma.', 'features' => ['Research', 'Wireframes', 'Prototypes', 'Design systems'], 'display_order' => 3],
            ['title' => 'Landing Pages', 'icon' => 'sparkles', 'description' => 'High-conversion pages that ship fast.', 'features' => ['Copy structure', 'A/B testing', 'SEO', 'Analytics'], 'display_order' => 4],
            ['title' => 'Backend API Engineering', 'icon' => 'server', 'description' => 'Clean, scalable REST APIs with Laravel & PostgreSQL.', 'features' => ['Clean Architecture', 'Sanctum Auth', 'PostgreSQL JSONB', 'Queue Jobs'], 'display_order' => 5],
            ['title' => 'Portfolio Websites', 'icon' => 'brain', 'description' => 'Personal brands that make an impression.', 'features' => ['Story-driven', 'CMS ready', 'Fast', 'SEO'], 'display_order' => 6],
        ];

        foreach ($servicesData as $srv) {
            Service::updateOrCreate(['title' => $srv['title']], $srv);
        }

        // 6. Create Certificates
        $certificatesData = [
            ['title' => 'Meta Frontend Developer', 'organization' => 'Meta / Coursera', 'issue_date' => '2024', 'credential_url' => '#', 'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&q=80', 'display_order' => 1],
            ['title' => 'Google UX Design', 'organization' => 'Google', 'issue_date' => '2023', 'credential_url' => '#', 'image' => 'https://images.unsplash.com/photo-1517842645767-c639042777db?w=800&q=80', 'display_order' => 2],
            ['title' => 'Deep Learning Specialization', 'organization' => 'DeepLearning.AI', 'issue_date' => '2024', 'credential_url' => '#', 'image' => 'https://images.unsplash.com/photo-1620712943543-bcc4688e7485?w=800&q=80', 'display_order' => 3],
            ['title' => 'AWS Cloud Practitioner', 'organization' => 'Amazon Web Services', 'issue_date' => '2023', 'credential_url' => '#', 'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&q=80', 'display_order' => 4],
        ];

        foreach ($certificatesData as $cert) {
            Certificate::updateOrCreate(['title' => $cert['title']], $cert);
        }

        // 7. Create Projects
        $projectsData = [
            [
                'title' => 'Nebula Analytics',
                'slug' => 'nebula-analytics',
                'description' => 'Real-time SaaS analytics dashboard with predictive AI insights.',
                'problem' => 'Modern SaaS applications struggle with rendering real-time cohort data.',
                'solution' => 'Built a high-performance streaming analytics interface with D3.js and TypeScript.',
                'technologies' => ['React', 'TypeScript', 'D3.js', 'Laravel'],
                'category' => 'Web App',
                'thumbnail' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=1200&q=80',
                'featured' => true,
                'status' => 'published',
                'github_url' => 'https://github.com/Aminullah-01',
                'live_url' => 'https://example.com',
                'display_order' => 1,
            ],
            [
                'title' => 'Prism Design System',
                'slug' => 'prism-design-system',
                'description' => 'Component library with 120+ accessible React primitives.',
                'problem' => 'Design inconsistencies across multi-repo product suites.',
                'solution' => 'Developed tokenized React components with automated Storybook testing.',
                'technologies' => ['React', 'Tailwind', 'Radix UI', 'Storybook'],
                'category' => 'Design System',
                'thumbnail' => 'https://images.unsplash.com/photo-1618761714954-0b8cd0026356?w=1200&q=80',
                'featured' => true,
                'status' => 'published',
                'github_url' => 'https://github.com/Aminullah-01',
                'live_url' => 'https://example.com',
                'display_order' => 2,
            ],
            [
                'title' => 'Loom AI Assistant',
                'slug' => 'loom-ai-assistant',
                'description' => 'Chat interface powered by fine-tuned LLM for research.',
                'problem' => 'Research teams lacked structured citations in AI responses.',
                'solution' => 'Implemented real-time streaming Markdown rendering with inline reference tooltips.',
                'technologies' => ['Next.js', 'Laravel', 'PostgreSQL', 'OpenAI'],
                'category' => 'AI',
                'thumbnail' => 'https://images.unsplash.com/photo-1677756119517-756a188d2d94?w=1200&q=80',
                'featured' => true,
                'status' => 'published',
                'github_url' => 'https://github.com/Aminullah-01',
                'live_url' => 'https://example.com',
                'display_order' => 3,
            ],
        ];

        foreach ($projectsData as $pData) {
            $p = Project::updateOrCreate(['slug' => $pData['slug']], $pData);

            ProjectImage::updateOrCreate(
                ['project_id' => $p->id, 'sort_order' => 1],
                [
                    'image_path' => $pData['thumbnail'],
                    'caption' => 'Overview Dashboard',
                ]
            );
        }

        // 8. Create Blog Posts
        $blogData = [
            [
                'title' => 'Designing for AI: 7 principles',
                'slug' => 'designing-for-ai',
                'category' => 'Design',
                'tags' => ['ai', 'ux'],
                'cover_image' => 'https://images.unsplash.com/photo-1677756119517-756a188d2d94?w=1200&q=80',
                'excerpt' => 'How to design interfaces that make AI feel trustworthy and useful.',
                'content' => "## Introduction\n\nDesigning for AI requires a fundamentally different mindset than standard deterministic UX...",
                'status' => 'published',
                'published_at' => now(),
            ],
            [
                'title' => 'Type-safe forms with React Hook Form',
                'slug' => 'type-safe-forms',
                'category' => 'Engineering',
                'tags' => ['react', 'typescript'],
                'cover_image' => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=1200&q=80',
                'excerpt' => 'A pragmatic guide to bulletproof forms.',
                'content' => "## Type Safety in Frontend Engineering\n\nForm validation can quickly become a bottleneck...",
                'status' => 'published',
                'published_at' => now()->subDays(5),
            ],
        ];

        foreach ($blogData as $b) {
            BlogPost::updateOrCreate(['slug' => $b['slug']], $b);
        }

        // 9. Create Contact Messages
        $messages = [
            [
                'name' => 'Sarah Chen',
                'email' => 'sarah@acme.com',
                'subject' => 'Freelance project inquiry',
                'message' => "Hi Aminu, I'd love to discuss a new SaaS product collaboration with you.",
                'is_read' => false,
                'archived' => false,
            ],
            [
                'name' => 'Marcus Rivera',
                'email' => 'marcus@studio.io',
                'subject' => 'Design system collaboration',
                'message' => "We're building a design system and would love your expertise.",
                'is_read' => true,
                'read_at' => now()->subDays(1),
                'archived' => false,
            ],
        ];

        foreach ($messages as $msg) {
            ContactMessage::updateOrCreate(
                ['email' => $msg['email'], 'subject' => $msg['subject']],
                $msg
            );
        }
    }
}
