<?php

/**
 * Smoke / feature checks for Elite Tech flows.
 * Run: php artisan tinker --execute="require 'tests/manual_smoke.php';"
 * Or:  php tests/manual_smoke.php
 */

use App\Models\Idea;
use App\Models\User;
use App\Models\Verification;
use App\Http\Controllers\ProfileController;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$failed = 0;
$ok = 0;

function check(string $label, bool $cond): void
{
    global $failed, $ok;
    if ($cond) {
        echo "[OK] $label\n";
        $ok++;
    } else {
        echo "[FAIL] $label\n";
        $failed++;
    }
}

// 1) asString must never explode on arrays
check('asString flattens arrays', ProfileController::asString(['a', ['b', 'c']]) === "a\nb\nc");
check('asSkills from string', ProfileController::asSkills('PHP, Laravel') === ['PHP', 'Laravel']);
check('asSkills from nested junk', ProfileController::asSkills([['x'], 'y']) === ['y']);

// 2) KYC re-evaluation
$owner = User::where('email', 'owner@elitetech.com')->first();
check('seed owner exists', (bool) $owner);

if ($owner) {
    $owner->forceFill([
        'kyc_status' => 'approved',
        'show_in_jobs_forum' => true,
        'wants_jobs_forum' => true,
        'title' => 'Old Title',
        'portfolio_url' => 'https://old.example',
    ])->save();

    $flagged = $owner->flagForKycRereview('اختبار: تغيير المسمى');
    $owner->refresh();
    check('flagForKycRereview returns true', $flagged);
    check('kyc becomes pending', $owner->kyc_status === 'pending');
    check('hidden from forum', $owner->show_in_jobs_forum === false);
    check('verification row created', Verification::where('user_id', $owner->id)->where('purpose', 'reevaluation')->exists());

    // restore for app use
    $owner->forceFill(['kyc_status' => 'approved', 'show_in_jobs_forum' => false])->save();
}

// 3) Idea fork parent link
$published = Idea::where('status', 'published')->first();
check('published idea exists', (bool) $published);

if ($published && $owner) {
    $fork = $published->replicate(['likes_count', 'admin_notes', 'status']);
    $fork->user_id = $owner->id;
    $fork->forked_from = $published->id;
    $fork->title = 'SMOKE Fork '.$published->id;
    $fork->status = 'draft';
    $fork->likes_count = 0;
    $fork->save();

    check('fork has parent_id', (int) $fork->forked_from === (int) $published->id);
    check('parent relation loads', $fork->fresh()->parent?->id === $published->id);

    $fork->delete();
}

// 4) HTTP smoke via kernel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$pages = ['/', '/login', '/register', '/ideas', '/jobs', '/about'];
foreach ($pages as $uri) {
    $request = Illuminate\Http\Request::create($uri, 'GET');
    $response = $kernel->handle($request);
    check("GET $uri => ".$response->getStatusCode(), $response->getStatusCode() === 200);
    $kernel->terminate($request, $response);
}

// Authenticated CV page must not 500
$demo = User::where('email', 'demo@elitetech.com')->first();
if ($demo) {
    // Corrupt CV experience as array to ensure view still works after fix
    \App\Models\Cv::updateOrCreate(
        ['user_id' => $demo->id],
        ['data' => [
            'title' => 'Dev',
            'summary' => 'bio',
            'skills' => ['PHP'],
            'experience' => ['line1', 'line2'], // previously caused htmlspecialchars crash
            'education' => 'Uni',
        ]]
    );

    Auth::login($demo);
    $request = Illuminate\Http\Request::create('/profile/cv-builder', 'GET');
    $request->setLaravelSession($app['session']->driver());
    $app['session']->start();
    $response = $kernel->handle($request);
    check('CV builder survives array experience', $response->getStatusCode() === 200);
    check('CV builder no htmlspecialchars error', ! str_contains($response->getContent(), 'htmlspecialchars'));
    $kernel->terminate($request, $response);
    Auth::logout();
}

echo "\nPassed: $ok / Failed: $failed\n";
exit($failed > 0 ? 1 : 0);
