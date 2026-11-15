<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // === Games ===
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['draft', 'active', 'finished'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // Session state (real-time)
            $table->string('session_status', 30)->default('lobby');
            // lobby | question_revealed | buzzer_open | buzzer_won | answering | judging | result | finished
            $table->foreignId('current_question_id')->nullable();
            $table->foreignId('buzzer_group_id')->nullable();
            $table->foreignId('current_answer_id')->nullable();
            $table->timestamp('status_changed_at')->nullable();
            $table->timestamp('next_action_at')->nullable(); // countdown auto-advance
            $table->integer('current_phase')->default(1);   // 1/2/3
            $table->integer('current_q_order')->default(0); // 0..29

            $table->timestamps();
        });

        // === Groups (kelompok) ===
        Schema::create('game_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->string('name', 50);                    // A, B, C...
            $table->foreignId('leader_id')->constrained('students')->cascadeOnDelete();
            $table->string('color', 20)->default('#ffcc33');
            $table->integer('score')->default(0);
            $table->timestamps();
        });

        // === Questions ===
        Schema::create('game_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('phase');        // 1, 2, 3
            $table->tinyInteger('order');        // 1-10
            $table->text('question_text');
            $table->string('answer_key', 200);
            $table->integer('points')->default(10);
            $table->timestamps();
            $table->unique(['game_id', 'phase', 'order']);
        });

        // === Answers ===
        Schema::create('game_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('game_groups')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('game_questions')->cascadeOnDelete();
            $table->text('answer_text')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->timestamp('buzzed_at')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamp('judged_at')->nullable();
            $table->timestamps();
        });

        // === History ===
        Schema::create('game_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->string('game_name');
            $table->foreignId('group_id')->nullable()->constrained('game_groups')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('score')->default(0);
            $table->integer('rank')->default(0);
            $table->string('role', 20); // leader | admin
            $table->timestamp('played_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_histories');
        Schema::dropIfExists('game_answers');
        Schema::dropIfExists('game_questions');
        Schema::dropIfExists('game_groups');
        Schema::dropIfExists('games');
    }
};
