<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Template header
        Schema::create('question_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Item soal di dalam template
        Schema::create('question_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('question_templates')->cascadeOnDelete();
            $table->tinyInteger('phase');       // 1, 2, 3
            $table->tinyInteger('order');       // 1-10
            $table->text('question_text');
            $table->string('answer_key', 200);
            $table->integer('points')->default(10);
            $table->timestamps();
            $table->unique(['template_id', 'phase', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_template_items');
        Schema::dropIfExists('question_templates');
    }
};
