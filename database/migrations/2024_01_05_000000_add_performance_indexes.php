<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ideas', function (Blueprint $t) {
            $t->index('status');
            $t->index(['user_id', 'status']);
        });

        Schema::table('messages', function (Blueprint $t) {
            $t->index(['sender_id', 'recipient_id']);
            $t->index(['recipient_id', 'read_at']);
        });

        Schema::table('verifications', function (Blueprint $t) {
            $t->index('status');
            $t->index(['user_id', 'status']);
        });

        Schema::table('users', function (Blueprint $t) {
            $t->index(['kyc_status', 'is_suspended']);
            $t->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropIndex(['kyc_status', 'is_suspended']);
            $t->dropIndex(['role']);
        });

        Schema::table('verifications', function (Blueprint $t) {
            $t->dropIndex(['status']);
            $t->dropIndex(['user_id', 'status']);
        });

        Schema::table('messages', function (Blueprint $t) {
            $t->dropIndex(['sender_id', 'recipient_id']);
            $t->dropIndex(['recipient_id', 'read_at']);
        });

        Schema::table('ideas', function (Blueprint $t) {
            $t->dropIndex(['status']);
            $t->dropIndex(['user_id', 'status']);
        });
    }
};
