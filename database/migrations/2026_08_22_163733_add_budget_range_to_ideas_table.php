<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ideas', function (Blueprint $table) {
            $table->decimal('budget_min', 12, 2)->nullable()->after('budget');
            $table->decimal('budget_max', 12, 2)->nullable()->after('budget_min');
            $table->string('currency', 5)->default('USD')->after('budget_max');
        });

        // Migrate existing budget data to budget_min
        DB::table('ideas')->whereNotNull('budget')->update([
            'budget_min' => DB::raw('budget'),
        ]);
    }

    public function down(): void
    {
        Schema::table('ideas', function (Blueprint $table) {
            $table->dropColumn(['budget_min', 'budget_max', 'currency']);
        });
    }
};
