<?php

namespace Database\Seeders;

use App\Models\Cv;
use App\Models\Idea;
use App\Models\IdeaComment;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $demo = User::updateOrCreate(
            ['email' => 'demo@elitetech.com'],
            [
                'name' => 'أحمد محمود',
                'password' => Hash::make('password'),
                'role' => 'developer',
                'roles' => ['developer', 'idea_seeker'],
                'title' => 'Full-stack Developer',
                'location' => 'القاهرة، مصر',
                'bio' => 'مطور شغوف ببناء منتجات ويب قابلة للتوسع والانضمام لمشاريع ناشئة جادة.',
                'email_verified_at' => now(),
                'kyc_status' => 'approved',
                'wants_jobs_forum' => true,
                'show_in_jobs_forum' => true,
                'available_for_hire' => true,
            ]
        );

        $owner = User::updateOrCreate(
            ['email' => 'owner@elitetech.com'],
            [
                'name' => 'سارة خالد',
                'password' => Hash::make('password'),
                'role' => 'idea_owner',
                'roles' => ['idea_owner'],
                'title' => 'رائدة أعمال تقنية',
                'location' => 'الرياض',
                'bio' => 'صاحبة أفكار في الأمن السيبراني والذكاء الاصطناعي.',
                'email_verified_at' => now(),
                'kyc_status' => 'approved',
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@elitetech.com'],
            [
                'name' => 'إدارة النظام',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'roles' => ['admin'],
                'title' => 'مدير النظام',
                'location' => 'القاهرة',
                'bio' => 'حساب الإدارة الرئيسي لمجتمع إليت تك.',
                'email_verified_at' => now(),
                'kyc_status' => 'approved',
            ]
        );

        $seeker = User::updateOrCreate(
            ['email' => 'seeker@elitetech.com'],
            [
                'name' => 'عمر خليل',
                'password' => Hash::make('password'),
                'role' => 'idea_seeker',
                'roles' => ['idea_seeker'],
                'title' => 'مطور مستقل',
                'location' => 'دبي',
                'bio' => 'أبحث عن أفكار قابلة للتنفيذ للشراكة.',
                'email_verified_at' => now(),
                'kyc_status' => 'approved',
            ]
        );

        Cv::updateOrCreate(
            ['user_id' => $demo->id],
            ['data' => [
                'title' => 'Full-stack Developer',
                'summary' => $demo->bio,
                'skills' => ['Laravel', 'Vue', 'PostgreSQL', 'Docker'],
                'experience' => "3 سنوات في بناء منصات SaaS\nمشاريع مفتوحة المصدر في المجتمع التقني",
                'education' => 'بكالوريوس علوم حاسب',
            ]]
        );

        $ideas = [
            [
                'user_id' => $owner->id,
                'title' => 'منصة كشف التهديدات المدعومة بالذكاء الاصطناعي',
                'category' => 'الأمن السيبراني',
                'description' => "منصة سحابية تحلل أمن الشركات الناشئة وتكتشف الثغرات تلقائياً قبل النشر.\n\nالمشكلة:\nالشركات الناشئة تنشر بدون مراجعة أمنية كافية.\n\nالحل:\nتحليل مستمر لحركة الشبكة مع تنبيهات فورية.",
                'feasibility' => 'تكلفة أولية على أدوات التجميع والتخزين. تقنيات مفتوحة المصدر مثل Kafka و TensorFlow متاحة.',
                'technologies' => ['Python', 'TensorFlow', 'Kafka', 'React'],
                'budget' => 49000,
                'status' => 'published',
                'likes_count' => 89,
            ],
            [
                'user_id' => $owner->id,
                'title' => 'مساعد شخصي للمبرمجين باللغة العربية',
                'category' => 'الذكاء الاصطناعي',
                'description' => "نموذج لغوي يفهم الاستفسارات البرمجية بالعربية ويولّد حلولاً بلغات متعددة.",
                'feasibility' => 'يتطلب بيانات تدريب عربية وجودة تقييم بشرية.',
                'technologies' => ['LLM', 'Python', 'FastAPI'],
                'budget' => 120000,
                'status' => 'published',
                'likes_count' => 315,
            ],
            [
                'user_id' => $demo->id,
                'title' => 'تطبيق ربط المطورين المستقلين بالمشاريع',
                'category' => 'تطبيقات الجوال',
                'description' => "بيئة آمنة لعرض المهارات والتقديم على مشاريع قصيرة مع ضمان مالي.",
                'technologies' => ['Flutter', 'Laravel', 'Stripe'],
                'budget' => 35000,
                'status' => 'published',
                'likes_count' => 210,
            ],
            [
                'user_id' => $seeker->id,
                'title' => 'شبكة بلوكتشين خضراء للشركات الناشئة',
                'category' => 'Blockchain',
                'description' => "بروتوكول إجماع يخفض استهلاك الطاقة بنسبة كبيرة مع الحفاظ على الأمان.",
                'technologies' => ['Solidity', 'Rust'],
                'budget' => 200000,
                'status' => 'pending',
                'likes_count' => 12,
            ],
        ];

        foreach ($ideas as $row) {
            $idea = Idea::updateOrCreate(
                ['title' => $row['title'], 'user_id' => $row['user_id']],
                $row
            );

            if ($idea->status === 'published' && $idea->comments()->count() === 0) {
                IdeaComment::create([
                    'idea_id' => $idea->id,
                    'user_id' => $demo->id,
                    'body' => 'فكرة واعدة — هل فكرتم في نموذج تسعير اشتراكي للشركات الصغيرة؟',
                ]);
            }
        }

        if (! Message::where('sender_id', $owner->id)->where('recipient_id', $demo->id)->exists()) {
            Message::create([
                'sender_id' => $owner->id,
                'recipient_id' => $demo->id,
                'body' => 'مرحباً أحمد، أعجبتني خبرتك. هل تهتم بالانضمام لمناقشة تنفيذ منصة كشف التهديدات؟',
            ]);
            Message::create([
                'sender_id' => $demo->id,
                'recipient_id' => $owner->id,
                'body' => 'أهلاً سارة، نعم مهتم. يمكننا ترتيب مكالمة قصيرة هذا الأسبوع.',
            ]);
            Message::create([
                'sender_id' => $seeker->id,
                'recipient_id' => $demo->id,
                'body' => 'شاهدت ملفك في منتدى التوظيف — هل أنت متاح لمشروع جزئي؟',
            ]);
        }
    }
}
