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
        // Add Xendit invoice fields to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->string('xendit_invoice_id')->nullable()->after('status_pembayaran');
            $table->string('xendit_payment_url')->nullable()->after('xendit_invoice_id');
            $table->string('xendit_payment_channel')->nullable()->after('xendit_payment_url');
            $table->timestamp('paid_at')->nullable()->after('xendit_payment_channel');
        });

        // Create topup_requests table for automated self topup via Xendit
        Schema::create('topup_requests', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'paid', 'expired', 'failed'])->default('pending');
            $table->string('xendit_invoice_id')->nullable()->index();
            $table->string('xendit_payment_url')->nullable();
            $table->string('payment_channel')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topup_requests');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['xendit_invoice_id', 'xendit_payment_url', 'xendit_payment_channel', 'paid_at']);
        });
    }
};
