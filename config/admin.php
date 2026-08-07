<?php

// Admin credentials from env — separate from the users table (session flag based).
// ADMIN_PASSWORD may be a plaintext value (legacy) or a bcrypt hash ($2y$...).
return [
    'email' => env('ADMIN_EMAIL', 'etech1596@gmail.com'),
    'password' => env('ADMIN_PASSWORD', 'elitetech_Admin_2026_Password'),
];
