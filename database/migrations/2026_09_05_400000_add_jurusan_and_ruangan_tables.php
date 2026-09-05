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
            $table->string('jurusan')->nullable()->after('kelas');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('ruangan_tujuan')->nullable()->after('kelas_tujuan');
            $table->string('jurusan_tujuan')->nullable()->after('ruangan_tujuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['jurusan']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['ruangan_tujuan', 'jurusan_tujuan']);
        });
    }
};
