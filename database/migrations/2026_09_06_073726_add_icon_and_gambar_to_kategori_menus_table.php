<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategori_menus', function (Blueprint $table) {
            $table->string('icon', 50)->nullable()->after('slug');
            $table->string('gambar')->nullable()->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('kategori_menus', function (Blueprint $table) {
            $table->dropColumn(['icon', 'gambar']);
        });
    }
};
