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
        Schema::create('diet_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title'); // 计划标题
            $table->date('date'); // 日期
            $table->json('meals')->nullable(); // 餐食详情，如早餐/午餐/晚餐（JSON）
            $table->integer('total_calories')->nullable(); // 总热量
            $table->integer('protein')->nullable(); // 蛋白质克数
            $table->integer('carbs')->nullable(); // 碳水克数
            $table->integer('fat')->nullable(); // 脂肪克数
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diet_plans');
    }
};
