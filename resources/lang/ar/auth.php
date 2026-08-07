<?php
// resources/lang/ar/auth.php
return [
    'welcome' => 'أهلاً بك في Elite Tech',
    'tagline' => 'منصة تشاركية شفافة — تصفح بحرية، وتفاعل بعد التوثيق.',
    'login_title' => 'تسجيل الدخول',
    'login_subtitle' => 'سجّل دخولك للمتابعة إلى مسارك في المجتمع',
    'new_account' => 'حساب جديد',
    'email' => 'البريد الإلكتروني',
    'password' => 'كلمة المرور',
    'forgot_password' => 'نسيت كلمة المرور؟',
    'remember_me' => 'تذكرني على هذا الجهاز',
    'submit_login' => 'دخول إلى المنصة',
    'or' => 'أو',

    'register_title' => 'إنشاء حساب جديد',
    'register_subtitle' => 'انضم لمجتمع النخبة التقنية',
    'name' => 'الاسم الكامل',
    'name_placeholder' => 'مثال: أحمد محمد',
    'email_placeholder' => 'name@example.com',
    'password_confirm' => 'تأكيد كلمة المرور',
    'password_confirm_placeholder' => 'أعد كتابة كلمة المرور',
    'terms_agree' => 'أوافق على',
    'terms_link' => 'الشروط والأحكام',
    'and' => 'و',
    'privacy_link' => 'سياسة الخصوصية',
    'submit_register' => 'إنشاء الحساب',
    'already_have_account' => 'لديك حساب بالفعل؟',

    'verify_title' => 'تأكيد البريد الإلكتروني',
    'verify_subtitle' => 'أرسلنا رمز تحقق من 6 أرقام إلى بريدك الإلكتروني',
    'otp_label' => 'رمز التحقق (6 أرقام)',
    'otp_placeholder' => '123456',
    'submit_verify' => 'تأكيد الرمز',
    'resend_code' => 'إعادة إرسال الرمز',
    'no_code_received' => 'لم يصلك الرمز؟',

    'path_title' => 'اختر مسارك',
    'path_subtitle' => 'اختر مسارًا أو أكثر — يمكنك تغييره لاحقاً',
    'path_submit' => 'حفظ المسار والمتابعة',
    'jobs_forum_interest' => 'أريد الظهور في منتدى التوظيف',

    'forgot_title' => 'استعادة كلمة المرور',
    'forgot_subtitle' => 'أدخل بريدك الإلكتروني وسنرسل لك رابط إعادة التعيين',
    'send_reset_link' => 'إرسال رابط الاسترداد',
    'back_to_login' => 'العودة لتسجيل الدخول',

    'reset_title' => 'تعيين كلمة مرور جديدة',
    'new_password' => 'كلمة المرور الجديدة',
    'confirm_password' => 'تأكيد كلمة المرور الجديدة',
    'submit_reset' => 'تعيين كلمة المرور',

    'admin_login_title' => 'لوحة الإدارة',
    'admin_login_subtitle' => 'دخول مخصص للإدارة',
    'admin_submit' => 'دخول لوحة الإدارة',

    // Credentials & account state
    'invalid_credentials'        => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
    'account_suspended'          => 'هذا الحساب معلّق. تواصل مع الإدارة.',
    'please_verify_email'        => 'يرجى التحقق من بريدك الإلكتروني للمتابعة.',
    'email_reserved'             => 'هذا البريد محجوز لحساب الإدارة.',
    'email_taken'                => 'هذا البريد مسجّل بالفعل. جرّب تسجيل الدخول.',
    'account_already_exists_login' => 'يبدو أن لديك حساباً بالفعل. يرجى تسجيل الدخول.',

    // Validation
    'terms_required'    => 'يجب الموافقة على الشروط للمتابعة.',
    'password_mismatch' => 'كلمتا المرور غير متطابقتين.',
    'role_required'     => 'يرجى اختيار مسار واحد على الأقل.',
    'otp_digits'        => 'رمز التحقق يجب أن يكون 6 أرقام.',
    'otp_required'      => 'يرجى إدخال رمز التحقق.',

    // OTP flow
    'session_expired_register'  => 'انتهت جلسة التسجيل. يرجى البدء من جديد.',
    'otp_expired_reregister'    => 'انتهت صلاحية الرمز. يرجى التسجيل مجدداً.',
    'otp_expired_resend'        => 'انتهت صلاحية الرمز. اطلب رمزاً جديداً.',
    'otp_wrong'                 => 'رمز التحقق غير صحيح.',
    'otp_wrong_attempts'        => 'رمز التحقق غير صحيح. المحاولات المتبقية: :count',
    'otp_max_attempts'          => 'تجاوزت الحد الأقصى للمحاولات. يرجى التسجيل مجدداً.',
    'otp_resent'                => 'تم إعادة إرسال رمز التحقق إلى بريدك الإلكتروني.',
    'email_verified_success'    => 'تم تأكيد البريد الإلكتروني وإنشاء حسابك بنجاح. 🎉',

    // Resend
    'resend_max_reached' => 'وصلت إلى الحد الأقصى لإعادة الإرسال. يرجى التسجيل مجدداً.',
    'resend_cooldown'    => 'يرجى الانتظار :seconds ثانية قبل إعادة الإرسال.',

    // Path selection
    'idea_owner_kyc_required' => 'مسار صاحب الفكرة يتطلب KYC قبل النشر. أكمل التحقق الآن.',
    'jobs_forum_kyc_required' => 'للانضمام لمنتدى التوظيف أكمل التحقق من الهوية.',
    'developer_path_selected' => 'يمكنك بناء سيرتك واستخراج PDF بحرية دون KYC.',
    'path_saved'              => 'تم حفظ مسارك بنجاح.',

    // Password reset
    'reset_link_sent'        => 'إن وُجد الحساب، تم إرسال رابط إعادة التعيين إلى بريدك.',
    'password_reset_success' => 'تم تحديث كلمة المرور. يمكنك تسجيل الدخول.',
    'reset_link_invalid'     => 'رابط إعادة التعيين غير صالح أو منتهٍ.',
    'email_not_found'        => 'هذا البريد الإلكتروني غير مسجل لدينا.',
    'otp_sent_reset'         => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني لإعادة تعيين كلمة المرور.',
];


