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
        Schema::create('purchase_voucher_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_voucher_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('service_id');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('qty');
            $table->string('unit');
            $table->decimal('rate', 12, 2);
            $table->timestamps();

            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('purchase_voucher_id')->references('id')->on('purchase_vouchers')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_voucher_details');
    }
};
