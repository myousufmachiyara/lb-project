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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // $table->unsignedBigInteger('acc_id');
            $table->integer('total_pcs');
            $table->text('description'); // Added description field
            $table->unsignedBigInteger('status_id');
            $table->timestamps();

            // $table->foreign('acc_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('project_status')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
