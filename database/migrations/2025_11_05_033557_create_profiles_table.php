<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('height_cm')->nullable();
            $table->decimal('weight_kg',5,2)->nullable();
            $table->integer('age')->nullable();
            $table->enum('gender',['male','female','other'])->nullable();
            $table->enum('activity_level',['sedentary','light','moderate','active','very_active'])->default('light');
            $table->decimal('target_weight_kg',5,2)->nullable();
            $table->date('target_date')->nullable();
            $table->json('preferences')->nullable(); // 用户偏好设置
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
}