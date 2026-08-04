<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('kyc_status')->default('none')->after('available_for_hire'); // none|pending|approved|rejected|suspended
            $t->string('kyc_purpose')->nullable()->after('kyc_status'); // publish_idea|implement|jobs_forum
            $t->boolean('wants_jobs_forum')->default(false)->after('kyc_purpose');
            $t->boolean('show_in_jobs_forum')->default(false)->after('wants_jobs_forum');
            $t->text('rejection_reason')->nullable()->after('show_in_jobs_forum');
            $t->text('admin_notes')->nullable()->after('rejection_reason');
            $t->boolean('is_suspended')->default(false)->after('admin_notes');
        });

        Schema::table('verifications', function (Blueprint $t) {
            $t->string('purpose')->nullable()->after('doc_type');
            $t->text('rejection_reason')->nullable()->after('status');
            $t->text('admin_notes')->nullable()->after('rejection_reason');
            $t->timestamp('reviewed_at')->nullable()->after('admin_notes');
        });

        Schema::table('ideas', function (Blueprint $t) {
            $t->text('admin_notes')->nullable()->after('likes_count');
        });

        Schema::create('implement_requests', function (Blueprint $t) {
            $t->id();
            $t->foreignId('idea_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->enum('via', ['elite_tech', 'idea_owner'])->default('idea_owner');
            $t->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $t->text('note')->nullable();
            $t->timestamps();
            $t->unique(['idea_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('implement_requests');
        Schema::table('ideas', function (Blueprint $t) {
            $t->dropColumn('admin_notes');
        });
        Schema::table('verifications', function (Blueprint $t) {
            $t->dropColumn(['purpose', 'rejection_reason', 'admin_notes', 'reviewed_at']);
        });
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn([
                'kyc_status', 'kyc_purpose', 'wants_jobs_forum', 'show_in_jobs_forum',
                'rejection_reason', 'admin_notes', 'is_suspended',
            ]);
        });
    }
};
