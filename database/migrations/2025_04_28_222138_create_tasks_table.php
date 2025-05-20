<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_name');
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('project_id')->default(0);
            $table->unsignedBigInteger('category_id')->default(0);
            $table->unsignedBigInteger('status_id')->default(0);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_frequency')->nullable(); // Still OK as string, though integer is better
            $table->date('last_completed_at')->nullable(); // ✅ New column for tracking completion
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
