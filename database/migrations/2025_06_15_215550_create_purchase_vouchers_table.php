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
        Schema::create('purchase_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_id')->nullable()->unique();
            $table->unsignedBigInteger('coa_id'); // FK to chart_of_accounts
            $table->date('date');
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('created_by'); // FK to users
            $table->timestamps();

            $table->foreign('coa_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_vouchers');
    }
};
