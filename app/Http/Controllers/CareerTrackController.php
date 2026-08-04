<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CareerTrackController extends Controller
{
    private function tracks(): array
    {
        return [
            ['slug' => 'idea-owner', 'title' => 'مسار صاحب فكرة', 'subtitle' => 'Idea Owner Track', 'icon' => '💡', 'status' => 'قيد المراجعة', 'statusColor' => 'bg-amber-50 text-amber-600 border-amber-200'],
            ['slug' => 'developer', 'title' => 'مسار مطور البرمجيات', 'subtitle' => 'Software Developer Track', 'icon' => '⟨/⟩', 'status' => 'قيد المراجعة', 'statusColor' => 'bg-blue-50 text-blue-600 border-blue-200'],
            ['slug' => 'idea-seeker', 'title' => 'مسار باحث عن فكرة', 'subtitle' => 'Idea Seeker Track', 'icon' => '🔍', 'status' => 'نشط', 'statusColor' => 'bg-emerald-50 text-emerald-600 border-emerald-200'],
        ];
    }

    private function trackDetails(): array
    {
        return [
            'idea-owner' => [
                'slug' => 'idea-owner',
                'title' => 'مسار صاحب فكرة',
                'subtitle' => 'متابعة حالة تفعيل مسارك الريادي والخطوات القادمة.',
                'statusLabel' => 'قيد المراجعة',
                'statusColor' => 'bg-amber-500 text-white shadow-glow',
                'projectName' => 'منصة «إبداع» للتقنيات العقارية',
                'projectCategory' => 'PropTech',
                'submissionDate' => '12 أكتوبر 2023',
                'needsAction' => false,
                'about' => 'مسار موجه لأصحاب الأفكار الطموحين لتفعيل نشاطهم كصنّاع أفكار في مجتمع النخبة.',
                'tags' => ['PropTech', 'Business Plan', 'Pitch Deck'],
                'steps' => [
                    [
                        'title' => 'تقديم الفكرة',
                        'desc' => 'تم استلام مسودة المشروع والبيانات الأساسية بنجاح.',
                        'state' => 'done',
                        'stateLabel' => 'مكتمل',
                        'icon' => '✓',
                    ],
                    [
                        'title' => 'دراسة الجدوى',
                        'desc' => 'يقوم فريق الخبراء حالياً بتقييم المخطط الأولي والقيمة السوقية.',
                        'state' => 'current',
                        'stateLabel' => 'قيد العمل حالياً...',
                        'icon' => '🔄',
                    ],
                    [
                        'title' => 'الموافقة النهائية',
                        'desc' => 'سيتم إصدار القرار النهائي بعد الانتهاء من الدراسة.',
                        'state' => 'pending',
                        'stateLabel' => 'في انتظار الخطوة السابقة',
                        'icon' => '🔒',
                    ],
                ],
            ],
            'developer' => [
                'slug' => 'developer',
                'title' => 'مسار مطور البرمجيات',
                'subtitle' => 'متابعة حالة تفعيل ملفك كمطور برمجيات نخبة.',
                'statusLabel' => 'قيد المراجعة',
                'statusColor' => 'bg-blue-600 text-white shadow-navy',
                'projectName' => 'ملف أعمال المطور البرمجي',
                'projectCategory' => 'Software Dev',
                'submissionDate' => '15 أكتوبر 2023',
                'needsAction' => true,
                'about' => 'هذا المسار مصمم للمطورين الذين يسعون للحصول على شارة «النخبة» في مجتمعنا. يتطلب المسار إثبات مهارات برمجية عالية.',
                'tags' => ['System Architecture', 'React & Node.js', 'Cloud Infrastructure'],
                'steps' => [
                    [
                        'title' => 'تقديم الطلب',
                        'desc' => 'تم استلام طلبك بنجاح.',
                        'state' => 'done',
                        'stateLabel' => 'مكتمل',
                        'icon' => '✓',
                    ],
                    [
                        'title' => 'مراجعة المعرض (Portfolio)',
                        'desc' => 'بانتظار إضافة رابط GitHub وملاحظات الخبرة.',
                        'state' => 'current',
                        'stateLabel' => 'قيد العمل حالياً...',
                        'icon' => '🔄',
                    ],
                    [
                        'title' => 'المابلة التقنية',
                        'desc' => 'سيتم تحديد الموعد بعد قبول الملف الشخصي.',
                        'state' => 'pending',
                        'stateLabel' => 'في انتظار الخطوة السابقة',
                        'icon' => '🔒',
                    ],
                ],
            ],
            'idea-seeker' => [
                'slug' => 'idea-seeker',
                'title' => 'مسار باحث عن فكرة',
                'subtitle' => 'متابعة حالة تفعيل حسابك كشريك ومستثمر في الأفكار.',
                'statusLabel' => 'نشط',
                'statusColor' => 'bg-emerald-600 text-white',
                'projectName' => 'ملف الشراكة والبحث',
                'projectCategory' => 'Invest & Partner',
                'submissionDate' => '01 نوفمبر 2023',
                'needsAction' => false,
                'about' => 'مسار لتفعيل دور الموجه أو الشريك في مشاريع مجتمع النخبة.',
                'tags' => ['Mentorship', 'Consultation', 'Partnership'],
                'steps' => [
                    [
                        'title' => 'تفعيل الحساب',
                        'desc' => 'تم التحقق من هويتك بنجاح.',
                        'state' => 'done',
                        'stateLabel' => 'مكتمل',
                        'icon' => '✓',
                    ],
                    [
                        'title' => 'اختيار المجالات',
                        'desc' => 'حدد اهتماماتك ومجالات خبرتك في صفحة الإعدادات.',
                        'state' => 'done',
                        'stateLabel' => 'مكتمل',
                        'icon' => '✓',
                    ],
                    [
                        'title' => 'مطابقة الأفكار',
                        'desc' => 'يمكنك البدء بطلب تنفيذ وتصفح بنك الأفكار.',
                        'state' => 'current',
                        'stateLabel' => 'نشط',
                        'icon' => '⚡',
                    ],
                ],
            ],
        ];
    }

    public function index(): \Illuminate\View\View
    {
        return view('career-tracks.index', ['tracks' => $this->tracks()]);
    }

    public function show(string $slug): \Illuminate\View\View
    {
        $data = $this->trackDetails();
        abort_unless(isset($data[$slug]), 404);

        return view('career-tracks.show', ['track' => $data[$slug]]);
    }

    public function update(string $slug, Request $request): \Illuminate\Http\RedirectResponse
    {
        abort_unless(in_array($slug, array_keys($this->trackDetails()), true), 404);

        return redirect()->route('career-tracks.show', $slug)->with('ok', 'تم تحديث البيانات المرفقة بنجاح.');
    }
}
