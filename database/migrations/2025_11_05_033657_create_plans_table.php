<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('plan_type',['workout','meal'])->default('workout');
            $table->date('date');
            $table->json('content'); // 训练步骤或食谱 JSON
            $table->timestamps();

            $table->unique(['user_id','plan_type','date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
