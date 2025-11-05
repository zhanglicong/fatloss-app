<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkoutPlansTable extends Migration
{
    public function up()
    {
        Schema::create('workout_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title'); // 计划标题
            $table->text('description')->nullable(); // 计划描述
            $table->string('target_muscle')->nullable(); // 目标肌群，如胸、腿、腹等
            $table->integer('duration')->nullable(); // 持续天数
            $table->json('schedule')->nullable(); // 每天的训练安排（JSON）
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('workout_plans');
    }
}
