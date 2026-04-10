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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $table->string('class_id');
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
            $table->integer('day_index');
            $table->string('lesson_name')->nullable();
            $table->timestamps();

            // Uma aula única por cronograma, turma e dia
            $table->unique(['schedule_id', 'class_id', 'day_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
