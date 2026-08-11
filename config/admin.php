<?php

// Admin credentials — loaded exclusively from environment variables.
// No fallback values: if env vars are missing, admin login is disabled.
// ADMIN_PASSWORD and ADMIN2_PASSWORD must be bcrypt hashes ($2y$...).
// Generate a hash with: php artisan tinker --execute="echo bcrypt('your-password');"
return [
    'email'     => env('ADMIN_EMAIL'),
    'password'  => env('ADMIN_PASSWORD'),
    'email2'    => env('ADMIN2_EMAIL'),
    'password2' => env('ADMIN2_PASSWORD'),
];
