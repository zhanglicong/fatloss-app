<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoalsTable extends Migration
{
    public function up()
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('goal_type',['fat_loss','muscle_gain','maintenance'])->default('fat_loss');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('start_weight',5,2)->nullable();
            $table->decimal('target_weight',5,2)->nullable();
            $table->json('meta')->nullable(); // pace, weekly_target 等
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('goals');
    }
}
