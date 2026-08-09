<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function landing()
    {
        $featuredIdeas = \App\Models\Idea::with(['user', 'parent.user'])
            ->where('status', 'published')
            ->orderByDesc('likes_count')
            ->take(3)
            ->get();

        $stats = [
            'ideas_published' => \App\Models\Idea::where('status', 'published')->count(),
            'talents_verified' => \App\Models\User::where('kyc_status', 'approved')->count(),
            'implement_requests' => \App\Models\ImplementRequest::count(),
        ];

        return view('pages.landing', compact('featuredIdeas', 'stats'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function terms()
    {
        $isAr = app()->getLocale() === 'ar';

        return view('pages.legal', [
            'title' => $isAr ? 'الشروط والأحكام' : 'Terms & Conditions',
            'updated' => $isAr ? 'يوليو 2026' : 'July 2026',
            'sections' => $isAr ? [
                ['h' => '1. القبول بالشروط', 'p' => 'باستخدامك لمنصة Elite Tech Community فإنك توافق على هذه الشروط والأحكام كاملة. إن لم توافق، يرجى عدم استخدام المنصة.'],
                ['h' => '2. طبيعة المنصة', 'p' => 'المنصة بيئة تشاركية لعرض الأفكار («حرق الأفكار») وبناء فرق العمل والتوظيف. الشركة توفّر البنية التقنية والتنظيمية ولا تضمن نجاح أي مشروع أو شراكة.'],
                ['h' => '3. إخلاء المسؤولية عن النزاعات الشخصية', 'p' => 'تخلي شركة «إليت تك» مسؤوليتها عن أي نزاعات شخصية أو تعاقدية تنشأ بين أصحاب الأفكار والمطورين عند اختيار التنفيذ بشكل مستقل أو بالشراكة المباشرة خارج وساطة المنصة. أي اتفاق خارجي يقع على مسؤولية أطرافه.'],
                ['h' => '4. قواعد النشر في بنك الأفكار', 'p' => 'يشترط لاجتياز المراجعة الإدارية: وضوح المشكلة والحل والمتطلبات التقنية، وعدم مخالفة سياسات المحتوى (احتيال، إساءة، انتهاك خصوصية، محتوى غير قانوني). يحق للإدارة رفض أو إرجاع أو إزالة أي فكرة مخالفة.'],
                ['h' => '5. التوثيق (KYC)', 'p' => 'بعض الصلاحيات (نشر فكرة، رغبة في التنفيذ، الظهور في منتدى التوظيف) تتطلب اجتياز التحقق. تقديم بيانات مضللة يؤدي إلى الرفض أو تعليق الحساب.'],
                ['h' => '6. الحسابات والتعليق', 'p' => 'يحق للإدارة تعليق أو سحب شارة التوثيق عند الاشتباه بالتحايل أو مخالفة السياسات، مع إمكانية طلب استكمال النواقص.'],
                ['h' => '7. التعديلات', 'p' => 'قد نحدّث هذه الشروط من وقت لآخر. استمرار الاستخدام بعد التحديث يعني الموافقة على النسخة السارية.'],
            ] : [
                ['h' => '1. Acceptance of Terms', 'p' => 'By using the Elite Tech Community platform, you agree to these Terms and Conditions in full. If you do not agree, please refrain from using the platform.'],
                ['h' => '2. Platform Nature', 'p' => 'The platform is a collaborative environment for brainstorming ("Idea Pitching"), team building, and employment. The company provides technical and organizational infrastructure and does not guarantee the success of any project or partnership.'],
                ['h' => '3. Disclaimer of Personal Disputes', 'p' => 'Elite Tech disclaims responsibility for any personal or contractual disputes that arise between idea owners and developers when opting for independent execution or direct partnership outside platform mediation. Any external agreement is solely the responsibility of its involved parties.'],
                ['h' => '4. Publishing Rules in the Ideas Bank', 'p' => 'To pass administrative review: clarity of the problem, solution, and technical requirements is required, as well as adherence to content policies (no fraud, abuse, privacy violation, or illegal content). Management reserves the right to reject, return, or remove any violating idea.'],
                ['h' => '5. Verification (KYC)', 'p' => 'Certain privileges (publishing an idea, requesting implementation, appearing in the job forum) require passing verification. Providing misleading information will lead to rejection or account suspension.'],
                ['h' => '6. Accounts and Suspension', 'p' => 'Management reserves the right to suspend accounts or revoke verification badges upon suspicion of fraud or policy violations, with the option to request rectifications.'],
                ['h' => '7. Amendments', 'p' => 'We may update these terms from time to time. Continued use of the platform after updates constitutes acceptance of the modified terms.'],
            ],
        ]);
    }

    public function privacy()
    {
        $isAr = app()->getLocale() === 'ar';

        return view('pages.legal', [
            'title' => $isAr ? 'سياسة الخصوصية' : 'Privacy Policy',
            'updated' => $isAr ? 'يوليو 2026' : 'July 2026',
            'sections' => $isAr ? [
                ['h' => '1. البيانات التي نجمعها', 'p' => 'بيانات الحساب (الاسم، البريد)، بيانات الملف المهني، رسائل التواصل داخل المنصة، وبيانات التحقق KYC (صور المستندات والصور الشخصية عند الرفع).'],
                ['h' => '2. بيانات KYC الحساسة', 'p' => 'مستندات التحقق تُخزَّن بشكل آمن على خوادم المنصة وتُعرض فقط لفرق الإدارة المختصة بمراجعة الطلبات. لا تُعرض للعامة ولا تُشارك مع أعضاء آخرين.'],
                ['h' => '3. التشفير والوصول', 'p' => 'نطبّق ضوابط وصول إدارية وجلسات منفصلة للوحة الإدارة. يُنصح بتغيير كلمات المرور الافتراضية في بيئات الإنتاج واستخدام HTTPS.'],
                ['h' => '4. ظهور السيرة الذاتية للعامة', 'p' => 'بناء الـ CV واستخراج PDF متاح للعضو بحرية. الظهور في «منتدى التوظيف» العام يحدث فقط بعد موافقة KYC وتفعيل الظهور. يمكنك إيقاف الظهور من إعداداتك.'],
                ['h' => '5. الاحتفاظ والحذف', 'p' => 'نحتفظ بالبيانات طالما الحساب نشط أو لأغراض الامتثال والمراجعة. يمكنك طلب إغلاق الحساب عبر التواصل مع الإدارة.'],
                ['h' => '6. التعديلات الحسّاسة', 'p' => 'تعديل المسمى الوظيفي أو روابط الأعمال أو رفع وثيقة جديدة قد يعيد الحساب لمراجعة KYC ويسحب شارة التوثيق مؤقتاً حمايةً من التحايل.'],
            ] : [
                ['h' => '1. Data We Collect', 'p' => 'Account data (name, email), professional profile information, internal platform messages, and KYC verification data (document copies and personal photos upon upload).'],
                ['h' => '2. Sensitive KYC Data', 'p' => 'Verification documents are securely stored on platform servers and accessed solely by authorized administrative teams reviewing applications. They are never displayed publicly nor shared with other members.'],
                ['h' => '3. Encryption and Access', 'p' => 'We enforce strict administrative access controls and isolated session handling for the admin panel. Changing default passwords in production environments and using HTTPS is strongly advised.'],
                ['h' => '4. Public CV Visibility', 'p' => 'Building CVs and exporting PDFs is freely available to members. Public listing on the "Job Forum" occurs only after KYC approval and enabling public visibility. You can turn off visibility at any time from your settings.'],
                ['h' => '5. Retention and Deletion', 'p' => 'We retain data as long as your account remains active or as required for compliance and auditing purposes. You may request account closure by contacting management.'],
                ['h' => '6. Sensitive Modifications', 'p' => 'Updating job title, portfolio links, or uploading new verification documents may trigger a re-review of KYC and temporarily pause verification status to protect against fraud.'],
            ],
        ]);
    }

    public function agreement()
    {
        $isAr = app()->getLocale() === 'ar';

        return view('pages.legal', [
            'title' => $isAr ? 'اتفاقية الاستخدام' : 'User Agreement',
            'updated' => $isAr ? 'يوليو 2026' : 'July 2026',
            'sections' => $isAr ? [
                ['h' => '1. ميثاق إلزامي', 'p' => 'هذه الاتفاقية ميثاق يوافق عليه العضو عند تقديم طلب «الرغبة في التنفيذ» أو عند إضافة/إرسال فكرة للنشر. الموافقة شرط لاستكمال الإجراء.'],
                ['h' => '2. احترام الملكية الفكرية', 'p' => 'يلتزم الأعضاء باحترام حقوق أصحاب الأفكار الأصلية وعدم انتحال المحتوى أو نسبه زوراً. النشر العلني يهدف للشفافية وليس لإسقاط الحقوق الأدبية.'],
                ['h' => '3. آلية استنساخ الأفكار (Forking)', 'p' => 'يحق لأي عضو مؤهّل البناء على فكرة منشورة عبر إنشاء سجل جديد مرتبط بـ parent_id للفكرة الأصلية. عند العرض تُظهر المنصة شارة واضحة: أن الفكرة مستلهمة/مبنية على فكرة [الأصلية] لصاحبها [الاسم]، حفظاً للتقدير الأدبي والشفافية.'],
                ['h' => '4. الجدية وحسن النية', 'p' => 'طلبات التنفيذ والتواصل يجب أن تكون بحسن نية. إساءة استخدام الرسائل أو التحايل على KYC تعرض الحساب للتعليق.'],
                ['h' => '5. العلاقة مع إليت تك', 'p' => 'عند اختيار التنفيذ عبر شركة إليت تك، تخضع الترتيبات لسياسات الشركة المنفصلة. عند الشراكة المباشرة مع صاحب الفكرة، تقع الالتزامات التعاقدية على الطرفين خارج مسؤولية المنصة ما لم يُتفق خلاف ذلك كتابياً.'],
            ] : [
                ['h' => '1. Binding Charter', 'p' => 'This agreement is a binding charter agreed to by members when submitting an "Implementation Request" or when creating/submitting an idea for publication. Agreement is required to proceed.'],
                ['h' => '2. Respect for Intellectual Property', 'p' => 'Members commit to respecting the rights of original idea owners and not plagiarizing or falsely claiming content ownership. Public publication aims for transparency and does not relinquish moral rights.'],
                ['h' => '3. Idea Forking Mechanism', 'p' => 'Any qualified member has the right to build upon a published idea by creating a new record linked to parent_id of the original idea. Upon display, the platform shows a clear badge: that the idea is inspired by/built on the original idea by [owner name], maintaining attribution and transparency.'],
                ['h' => '4. Seriousness and Good Faith', 'p' => 'Implementation requests and communications must be conducted in good faith. Misuse of messaging or circumventing KYC subjects the account to suspension.'],
                ['h' => '5. Relationship with Elite Tech', 'p' => 'When choosing implementation through Elite Tech, arrangements are subject to separate company policies. In direct partnerships with idea owners, contractual obligations lie solely on both parties outside platform responsibility unless agreed otherwise in writing.'],
            ],
        ]);
    }
}
