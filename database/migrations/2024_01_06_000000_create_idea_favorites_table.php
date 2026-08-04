<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('idea_favorites', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('idea_id')->constrained()->cascadeOnDelete();
            $t->timestamps();
            $t->unique(['user_id', 'idea_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idea_favorites');
    }
};
