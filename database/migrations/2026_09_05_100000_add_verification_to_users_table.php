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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false)->after('is_active');
            $table->string('kartu_pelajar_photo')->nullable()->after('is_verified');
            $table->timestamp('verified_at')->nullable()->after('kartu_pelajar_photo');
            $table->string('verification_note')->nullable()->after('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'kartu_pelajar_photo', 'verified_at', 'verification_note']);
        });
    }
};
