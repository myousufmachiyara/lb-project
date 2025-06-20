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
        Schema::create('gatepass_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gatepass_id');
            $table->string('image')->nullable();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('service_id');
            $table->text('description')->nullable();
            $table->integer('qty');
            $table->string('unit', 50);
            $table->decimal('rate', 10, 2);
            $table->timestamps();

            $table->foreign('gatepass_id')->references('id')->on('gatepasses')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gatepass_details');
    }
};
