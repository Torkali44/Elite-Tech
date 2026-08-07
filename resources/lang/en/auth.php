<?php
// resources/lang/en/auth.php
return [
    'welcome' => 'Welcome to Elite Tech',
    'tagline' => 'A transparent collaborative platform — browse freely, interact after verification.',
    'login_title' => 'Sign In',
    'login_subtitle' => 'Sign in to continue to your path in the community',
    'new_account' => 'New Account',
    'email' => 'Email Address',
    'password' => 'Password',
    'forgot_password' => 'Forgot your password?',
    'remember_me' => 'Remember me on this device',
    'submit_login' => 'Sign In',
    'or' => 'Or',

    'register_title' => 'Create a New Account',
    'register_subtitle' => 'Join the elite tech community',
    'name' => 'Full Name',
    'name_placeholder' => 'e.g. Ahmed Mohamed',
    'email_placeholder' => 'name@example.com',
    'password_confirm' => 'Confirm Password',
    'password_confirm_placeholder' => 'Re-enter your password',
    'terms_agree' => 'I agree to the',
    'terms_link' => 'Terms & Conditions',
    'and' => 'and',
    'privacy_link' => 'Privacy Policy',
    'submit_register' => 'Create Account',
    'already_have_account' => 'Already have an account?',

    'verify_title' => 'Verify Your Email',
    'verify_subtitle' => 'We sent a 6-digit verification code to your email',
    'otp_label' => 'Verification Code (6 digits)',
    'otp_placeholder' => '123456',
    'submit_verify' => 'Verify Code',
    'resend_code' => 'Resend Code',
    'no_code_received' => "Didn't receive the code?",

    'path_title' => 'Choose Your Path',
    'path_subtitle' => 'Select one or more paths — you can change later',
    'path_submit' => 'Save Path & Continue',
    'jobs_forum_interest' => 'I want to appear in the Jobs Forum',

    'forgot_title' => 'Recover Password',
    'forgot_subtitle' => 'Enter your email and we will send you a reset link',
    'send_reset_link' => 'Send Reset Link',
    'back_to_login' => 'Back to Login',

    'reset_title' => 'Set a New Password',
    'new_password' => 'New Password',
    'confirm_password' => 'Confirm New Password',
    'submit_reset' => 'Reset Password',

    'admin_login_title'    => 'Admin Panel',
    'admin_login_subtitle' => 'Restricted access for administrators',
    'admin_submit'         => 'Access Admin Panel',

    // Credentials & account state
    'invalid_credentials'          => 'Incorrect email or password.',
    'account_suspended'            => 'This account is suspended. Contact support.',
    'please_verify_email'          => 'Please verify your email to continue.',
    'email_reserved'               => 'This email is reserved for the admin account.',
    'email_taken'                  => 'This email is already registered. Try signing in.',
    'account_already_exists_login' => 'An account with this email already exists. Please sign in.',

    // Validation
    'terms_required'    => 'You must accept the terms to continue.',
    'password_mismatch' => 'Passwords do not match.',
    'role_required'     => 'Please select at least one path.',
    'otp_digits'        => 'The verification code must be exactly 6 digits.',
    'otp_required'      => 'Please enter the verification code.',

    // OTP flow
    'session_expired_register'  => 'Registration session expired. Please start over.',
    'otp_expired_reregister'    => 'Code expired. Please register again.',
    'otp_expired_resend'        => 'Code expired. Please request a new one.',
    'otp_wrong'                 => 'Incorrect verification code.',
    'otp_wrong_attempts'        => 'Incorrect code. Attempts remaining: :count',
    'otp_max_attempts'          => 'Maximum verification attempts exceeded. Please register again.',
    'otp_resent'                => 'A new verification code has been sent to your email.',
    'email_verified_success'    => 'Email verified and account created successfully! 🎉',

    // Resend
    'resend_max_reached' => 'Maximum resend attempts reached. Please register again.',
    'resend_cooldown'    => 'Please wait :seconds seconds before resending.',

    // Path selection
    'idea_owner_kyc_required' => 'The Idea Owner path requires KYC before publishing. Complete verification now.',
    'jobs_forum_kyc_required' => 'To join the Jobs Forum, complete identity verification.',
    'developer_path_selected' => 'You can freely build your CV and export a PDF without KYC.',
    'path_saved'              => 'Your path has been saved successfully.',

    // Password reset
    'reset_link_sent'        => 'If the account exists, a reset link has been sent to your email.',
    'password_reset_success' => 'Password updated. You can now sign in.',
    'reset_link_invalid'     => 'The reset link is invalid or has expired.',
    'email_not_found'        => 'This email address is not registered.',
    'otp_sent_reset'         => 'A verification code has been sent to your email to reset your password.',
];

