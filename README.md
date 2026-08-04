# Elite Tech Community — منصة مجتمع النخبة التقنية

مشروع Laravel 11 + Blade لمنصة "Elite Tech Community" (بنك أفكار، مجتمع مواهب، مسارات مهنية، KYC، بناء CV، لوحات تحكم متعددة الأدوار).

---

## 🆕 آخر التعديلات (الجولة الثانية)

كانت هذه التعديلات رداً مباشراً على الملاحظات + ملف `EliteCommunityPRD.html`:

| المشكلة | الحل |
|---|---|
| أي حد يقدر يفتح أي صفحة من الـ URL مباشرة | كل الراوتس بقت متقسمة على `guest` / `auth` / `role:` / `admin.auth` middleware. جرّب تفتح `/dashboard` من غير تسجيل دخول هتترمي على `/login` تلقائياً |
| تسجيل الدخول مش شغال فعلياً | `AuthController` بقى بيستخدم `Auth::attempt()` / `Hash::make()` / `Auth::login()` فعلياً على جدول `users`، مش مجرد Redirect وهمي |
| الأدمن كان جوه نفس تسجيل الدخول بتاع اليوزرز | الأدمن بقى ليه Login منفصل تماماً على `/admin/login`، بإيميل/باسورد ثابتين من `.env` (`ADMIN_EMAIL` / `ADMIN_PASSWORD`)، ومحفوظ بجلسة منفصلة (`is_admin`) مش هو حساب في جدول `users` أصلاً |
| لوحة تحكم الأدمن مش ثابتة | اتعملها `layouts/admin.blade.php` مستقل تماماً عن `layouts/dashboard.blade.php` بتاع الأعضاء، بسايدبار ثابت (نظرة عامة / المستخدمون / الأفكار / KYC / البلاغات) |
| فورم "إضافة فكرة" حقوله ناقصة | اتعمل من جديد: عنوان، وصف مختصر، **المشكلة** و**الحل** كحقلين منفصلين، فئة، ميزانية، Technical Stack كـ tags قابلة للإضافة/الحذف، دراسة جدوى، رابط مرجعي، رفع ملفات (Dropzone)، وزرار "حفظ كمسودة" منفصل عن "نشر الفكرة"، مع Validation فعلي في الكنترولر |
| صفحة السيرة الذاتية شكلها ثابت | بقت تفاعلية بالكامل بـ Alpine.js: أي حقل تكتبه (بيانات شخصية / خبرات متعددة / تعليم متعدد / مهارات) بيظهر فوراً في معاينة حية بجانب الفورم، وزرار "تحميل PDF" بيصدّر المعاينة كـ PDF عبر طباعة المتصفح (Print CSS مخصص) |
| التصميم مش Responsive بالكامل | اتضاف قائمة موبايل (Hamburger menu) في الـ Navbar العام اللي كانت ناقصة تماماً، ومراجعة باقي الصفحات على `sm:` / `md:` / `lg:` |
| صفحات ناقصة | اتضافت صفحة "الرسائل / الشبكة" (Inbox) الظاهرة في السكرين شوتس، مع Model و Migration خاصين بيها (`Message`, `Connection`) |

---

## 🧩 التقنيات
- **Laravel 11 (Blade)** — تم استكمال ملفات الـ Runtime الناقصة (`artisan`, `bootstrap/app.php`, `public/index.php`, `config/*`) عشان المشروع يشتغل فعلياً بـ `php artisan serve`
- **Tailwind CSS** عبر CDN (سهل تحويله لـ Vite build لاحقاً)
- **Alpine.js** للتفاعلات (Sidebar, Tabs, Tags, Live CV Preview, Mobile Menu)
- **SQLite** افتراضياً للتطوير السريع (تقدر تغيّرها لـ MySQL من `.env`)
- RTL كامل + خط `Cairo`

## 🎨 الألوان (Design Tokens)
| الاسم | القيمة |
|-------|--------|
| Primary (Navy) | `#1A365D` |
| Secondary (Orange) | `#F6993F` |
| Tertiary | `#4A5568` |
| Neutral BG | `#F7FAFC` |

## 👥 الأدوار (Roles)
1. **admin** — لوحة تحكم منفصلة تماماً (`/admin/login`)، مش عضو عادي
2. **idea_owner** — صاحب فكرة
3. **idea_seeker** — باحث عن فكرة / موجّه
4. **developer** — باحث عن عمل / مطور

---

## 🚀 التشغيل

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

افتح: `http://localhost:8000`

- **حساب عضو تجريبي** (من الـ Seeder): `demo@elitetech.com` / `password`
- **دخول لوحة الأدمن**: `/admin/login` — عيّن `ADMIN_EMAIL` و `ADMIN_PASSWORD` في `.env` (يفضّل bcrypt hash لكلمة المرور)

> ⚠️ لا تضع بيانات إدارة افتراضية في الإنتاج. غيّر `ADMIN_PASSWORD` فوراً ويفضّل تخزينها كـ bcrypt hash.

---

## 🗂️ الاستراكتشر

```
elitetech/
├── app/
│   ├── Http/Controllers/       ← كنترولر لكل قسم (+ AdminAuthController الجديد)
│   ├── Http/Middleware/
│   │   ├── RoleMiddleware.php  ← يتحقق من دور العضو (idea_owner / developer / ..)
│   │   ├── EnsureAdmin.php     ← يحمي كل /admin/* (جلسة is_admin)
│   │   └── RedirectIfAdmin.php ← يمنع الأدمن المسجل دخول من الرجوع لصفحة اللوجين
│   └── Models/                 ← User, Idea, Comment, CareerTrack, Verification, Cv,
│                                  Connection, Message (جديد)
├── database/
│   ├── migrations/              ← core tables + network tables (رسائل/تواصل)
│   └── seeders/DatabaseSeeder.php
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php        ← لايوت عام (Public) + قائمة موبايل جديدة
│   │   ├── auth.blade.php       ← لايوت صفحات المصادقة (تستخدمها صفحة أدمن-لوجين برضه)
│   │   ├── dashboard.blade.php  ← لايوت لوحة تحكم الأعضاء (سايدبار يمين)
│   │   └── admin.blade.php      ← لايوت لوحة تحكم الأدمن (منفصل تماماً) [جديد]
│   ├── components/               ← عناصر UI قابلة لإعادة الاستخدام [جديد]
│   │   ├── stat-card.blade.php
│   │   ├── badge.blade.php
│   │   ├── button.blade.php
│   │   ├── idea-card.blade.php
│   │   └── stepper-step.blade.php
│   ├── network/index.blade.php  ← صندوق الرسائل / طلبات التواصل [جديد]
│   ├── admin/                   ← login.blade.php [جديد] + dashboard + table
│   └── ... (باقي الأقسام زي القديم: auth, ideas, community, mentors, jobs,
│            career-tracks, verification, profile, settings, dashboards)
└── routes/web.php               ← مُعاد تنظيمه بالكامل بمجموعات middleware
```

## 🗺️ خريطة الصلاحيات على الراوتس

| المجموعة | Middleware | أمثلة |
|---|---|---|
| عام (زوّار + أعضاء) | — | `/`, `/about`, `/ideas`, `/ideas/{id}`, `/community`, `/mentors`, `/jobs`, `/profile/{id}` |
| صفحات مصادقة (زوّار فقط) | `guest` | `/login`, `/register`, `/forgot-password` |
| بعد التسجيل مباشرة | `auth` | `/auth/verify`, `/auth/path-selection` |
| منطقة الأعضاء | `auth` | `/dashboard`, `/ideas/create`, `/ideas/{id}/fork`, `/career-tracks/*`, `/verification/kyc`, `/profile/cv-builder`, `/settings`, `/network` |
| لوحات حسب الدور | `auth` + `role:xxx` | `/dashboard/idea-owner` (`idea_owner`), `/dashboard/developer` (`developer`) |
| لوحة الأدمن | `admin.auth` | `/admin/dashboard`, `/admin/users`, `/admin/ideas`, `/admin/ideas/{id}`, `/admin/verifications`, `/admin/implementations`, `/admin/implementations/{id}`, `/admin/reports` |

---

## 🚀 التحديثات الأخيرة (الإدارة والمؤشرات)
- إتاحة عرض تفصيلي شامل للأفكار (`/admin/ideas/{id}`) وطلبات التنفيذ (`/admin/implementations/{id}`) بداخل لوحة الإدارة.
- إضافة إمكانية تعليق وتفعيل الحسابات من جدول المستخدمين مع تمييز لوني واضح.
- إضافة مؤشر `أكثر من مسار` لتمييز المستخدمين الذين يملكون أدوار متعددة.
- تحديث مؤشرات لوحة الإدارة لإظهار **معدل التحويل (Conversion Rate)** و **متوسط SLA لطلبات KYC**.
- تخطي صفحة KYC كلياً لمسار "باحث عن فكرة".

---

## ✅ الحالة الحالية / المطلوب لاحقاً

- **مُنفّذ بالكامل:** تسجيل دخول/خروج حقيقي، حماية الراوتس بالكامل، فصل الأدمن، فورم الأفكار الكامل، معاينة CV حية + PDF، صفحة الرسائل، تصميم متجاوب على كل الصفحات الأساسية.
- **Scaffolding جاهز لكن بدون DB حقيقي بالكامل:** بنك الأفكار / المجتمع / الوظائف لسه بتعرض بيانات ثابتة (dummy arrays) في الكنترولرات بدل استعلامات DB فعلية — الجداول والـ Models جاهزة (`Idea`, `IdeaComment`, `CareerTrack`, `Verification`) وتحتاج فقط ربط الكنترولرات بيها + Seeders لبيانات تجريبية.
- **إجراءات الأدمن** (نشر فكرة / رفض توثيق / تعليق حساب) الأزرار شغالة وبترجع رسالة نجاح، لكن لسه محتاجة تتوصل فعلياً بالـ Models بدل ما تكون stub.
- **OTP الحقيقي:** خطوة `/auth/verify` حالياً بتقبل أي كود من 6 أرقام (تأكيد فوري) بدل إرسال بريد حقيقي — تقدر تربطها بـ Laravel Notifications لاحقاً بسهولة.

هذا التقسيم اتعمل عشان تقدر تكمل الـ backend تدريجياً بدون ما تكسر أي حاجة من التصميم أو الـ Routes.
