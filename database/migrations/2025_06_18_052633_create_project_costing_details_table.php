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
        Schema::create('project_costing_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_costing_id');
            $table->unsignedBigInteger('service_id');
            $table->string('description')->nullable();
            $table->integer('qty');
            $table->decimal('rate', 10, 2);
            $table->decimal('service_percent', 5, 2);
            $table->timestamps();

            $table->foreign('project_costing_id')->references('id')->on('project_costings')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_costing_details');
    }
};
