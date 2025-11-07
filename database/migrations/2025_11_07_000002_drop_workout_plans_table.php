<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('workout_plans');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 由于这是破坏性操作，我们不提供回滚
    }
};