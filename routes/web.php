<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    PageController,
    AuthController,
    AdminAuthController,
    IdeaController,
    CommunityController,
    MentorController,
    JobController,
    DashboardController,
    CareerTrackController,
    VerificationController,
    ProfileController,
    SettingsController,
    NetworkController,
    AdminController,
    LanguageController
};

Route::get('/lang/{locale}', [LanguageController::class, 'switch'])
    ->name('lang.switch')
    ->where('locale', 'ar|en');



Route::get('/', [PageController::class, 'landing'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/agreement', [PageController::class, 'agreement'])->name('agreement');

// Public browse (Guest Mode)
Route::get('/ideas', [IdeaController::class, 'index'])->name('ideas.index');
Route::get('/community', [CommunityController::class, 'index'])->name('community');
Route::get('/community/{id}', [CommunityController::class, 'show'])->name('community.show');
Route::get('/mentors', [MentorController::class, 'index'])->name('mentors');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs');
Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show')->whereNumber('id');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::get('/forgot-password', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/reset-password/{token?}', [AuthController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:5,1');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Verify routes: accessible by GUESTS (pending_reg in session) AND authenticated users.
// They must NOT be inside the 'auth' middleware group.
Route::get('/auth/verify', [AuthController::class, 'showVerify'])->name('auth.verify')->middleware('throttle:10,1');
Route::post('/auth/verify', [AuthController::class, 'verify'])->middleware('throttle:5,1');

Route::middleware('auth')->group(function () {
    Route::get('/auth/path-selection', [AuthController::class, 'showPathSelection'])->name('auth.path');
    Route::post('/auth/path-selection', [AuthController::class, 'savePath']);

    // Ideas — create BEFORE {id} so it is not captured as show
    Route::get('/ideas/create', [IdeaController::class, 'create'])->name('ideas.create');
    Route::post('/ideas', [IdeaController::class, 'store'])->name('ideas.store');
    Route::get('/ideas/{id}/edit', [IdeaController::class, 'edit'])->name('ideas.edit')->whereNumber('id');
    Route::put('/ideas/{id}', [IdeaController::class, 'update'])->name('ideas.update')->whereNumber('id');
    Route::post('/ideas/{id}/submit', [IdeaController::class, 'submitDraft'])->name('ideas.submit')->whereNumber('id');
    Route::get('/ideas/{id}/fork', [IdeaController::class, 'forkConfirm'])->name('ideas.fork.confirm')->whereNumber('id');
    Route::post('/ideas/{id}/fork', [IdeaController::class, 'fork'])->name('ideas.fork')->whereNumber('id');
    Route::get('/ideas/{id}/implement', [IdeaController::class, 'implementForm'])->name('ideas.implement.form')->whereNumber('id');
    Route::get('/ideas/{id}/comment', [IdeaController::class, 'commentPage'])->name('ideas.comment.page')->whereNumber('id');
    Route::post('/ideas/{id}/comment', [IdeaController::class, 'comment'])->name('ideas.comment')->whereNumber('id');
    Route::post('/ideas/{id}/implement', [IdeaController::class, 'implement'])->name('ideas.implement')->whereNumber('id');
    Route::post('/ideas/{id}/favorite', [IdeaController::class, 'toggleFavorite'])->name('ideas.favorite')->whereNumber('id');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/idea-owner', [DashboardController::class, 'ideaOwner'])
        ->middleware('role:idea_owner')->name('dashboard.ideaOwner');
    Route::get('/dashboard/implement-requests', [DashboardController::class, 'implementRequests'])
        ->middleware('role:idea_owner')->name('dashboard.implementRequests');
    Route::post('/dashboard/implement-requests/{id}/respond', [DashboardController::class, 'respondImplement'])
        ->middleware('role:idea_owner')->name('dashboard.implementRespond');
    Route::get('/dashboard/idea-seeker', [DashboardController::class, 'ideaSeeker'])
        ->middleware('role:idea_seeker')->name('dashboard.ideaSeeker');
    Route::get('/dashboard/developer', [DashboardController::class, 'developer'])
        ->middleware('role:developer')->name('dashboard.developer');

    Route::get('/career-tracks', [CareerTrackController::class, 'index'])->name('career-tracks.index');
    Route::get('/career-tracks/{slug}', [CareerTrackController::class, 'show'])->name('career-tracks.show');
    Route::post('/career-tracks/{slug}/update', [CareerTrackController::class, 'update'])->name('career-tracks.update');

    Route::get('/verification/kyc', [VerificationController::class, 'show'])->name('verification.kyc');
    Route::post('/verification/kyc', [VerificationController::class, 'submit']);

    Route::get('/profile/cv-builder', [ProfileController::class, 'cvBuilder'])->name('profile.cv');
    Route::post('/profile/cv-builder', [ProfileController::class, 'saveCv']);

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update']);

    Route::get('/network', [NetworkController::class, 'index'])->name('network.index');
    Route::post('/network/start', [NetworkController::class, 'start'])->name('network.start');
    Route::post('/network/{id}/reply', [NetworkController::class, 'reply'])->name('network.reply');
    Route::post('/network/{id}/archive', [NetworkController::class, 'archive'])->name('network.archive');
});

// Public idea detail AFTER /ideas/create
Route::get('/ideas/{id}', [IdeaController::class, 'show'])->name('ideas.show')->whereNumber('id');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/login', '/login');

    Route::middleware('admin.auth')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users/{id}/suspend', [AdminController::class, 'suspendUser'])->name('users.suspend');
        Route::post('/users/{id}/activate', [AdminController::class, 'activateUser'])->name('users.activate');
        Route::post('/users/{id}/notes', [AdminController::class, 'saveUserNotes'])->name('users.notes');
        Route::get('/ideas', [AdminController::class, 'ideas'])->name('ideas');
        Route::get('/ideas/{id}', [AdminController::class, 'showIdea'])->name('ideas.show')->whereNumber('id');
        Route::post('/ideas/{id}/publish', [AdminController::class, 'publishIdea'])->name('ideas.publish');
        Route::post('/ideas/{id}/return', [AdminController::class, 'returnIdea'])->name('ideas.return');
        Route::get('/verifications', [AdminController::class, 'verifications'])->name('verifications');
        Route::get('/verifications/{id}/file/{field}', [AdminController::class, 'document'])->name('verifications.file');
        Route::post('/verifications/{id}/approve', [AdminController::class, 'approveVerification'])->name('verifications.approve');
        Route::post('/verifications/{id}/reject', [AdminController::class, 'rejectVerification'])->name('verifications.reject');
        Route::get('/implementations', [AdminController::class, 'implementations'])->name('implementations');
        Route::get('/implementations/{id}', [AdminController::class, 'showImplementation'])->name('implementations.show')->whereNumber('id');
        Route::post('/implementations/{id}/approve', [AdminController::class, 'approveImplementation'])->name('implementations.approve');
        Route::post('/implementations/{id}/reject', [AdminController::class, 'rejectImplementation'])->name('implementations.reject');
        Route::post('/comments/{id}/delete', [AdminController::class, 'deleteComment'])->name('comments.delete');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    });
});
