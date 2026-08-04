<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name'); $t->string('email')->unique();
            $t->timestamp('email_verified_at')->nullable();
            $t->string('password');
            $t->enum('role', ['admin','idea_owner','idea_seeker','developer'])->default('developer');
            $t->json('roles')->nullable();
            $t->string('title')->nullable(); $t->text('bio')->nullable();
            $t->string('location')->nullable(); $t->string('avatar')->nullable();
            $t->boolean('available_for_hire')->default(true);
            $t->rememberToken(); $t->timestamps();
        });

        Schema::create('ideas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('forked_from')->nullable()->constrained('ideas')->nullOnDelete();
            $t->string('title'); $t->string('category');
            $t->text('description'); $t->text('feasibility')->nullable();
            $t->json('technologies')->nullable();
            $t->decimal('budget',12,2)->nullable();
            $t->enum('status',['draft','pending','published','archived'])->default('pending');
            $t->unsignedInteger('likes_count')->default(0);
            $t->timestamps();
        });

        Schema::create('idea_comments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('idea_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->text('body'); $t->timestamps();
        });

        Schema::create('career_tracks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('slug'); $t->enum('status',['pending','current','done','rejected'])->default('pending');
            $t->text('admin_notes')->nullable(); $t->string('github')->nullable();
            $t->timestamps();
        });

        Schema::create('verifications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('doc_type'); $t->string('id_front')->nullable();
            $t->string('id_back')->nullable(); $t->string('selfie')->nullable();
            $t->enum('status',['pending','approved','rejected'])->default('pending');
            $t->timestamps();
        });

        Schema::create('cvs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->json('data'); $t->json('visibility')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        foreach(['cvs','verifications','career_tracks','idea_comments','ideas','users'] as $t) Schema::dropIfExists($t);
    }
};
