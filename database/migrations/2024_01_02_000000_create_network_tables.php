<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('connections', function (Blueprint $t) {
            $t->id();
            $t->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $t->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $t->text('note')->nullable();
            $t->timestamps();
        });

        Schema::create('messages', function (Blueprint $t) {
            $t->id();
            $t->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $t->text('body');
            $t->timestamp('read_at')->nullable();
            $t->boolean('archived')->default(false);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('connections');
    }
};
