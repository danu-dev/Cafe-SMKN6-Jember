<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_menus', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori_menus')->cascadeOnDelete();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 12, 2);
            $table->string('gambar')->nullable();
            $table->integer('stok')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->decimal('saldo', 12, 2)->default(0)->after('role');
            $table->boolean('is_active')->default(true)->after('saldo');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan')->unique()->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kurir_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('tipe_pengiriman', ['antar', 'ambil'])->default('antar');
            $table->string('kelas_tujuan')->nullable();
            $table->enum('metode_pembayaran', ['saldo', 'cod'])->default('saldo');
            $table->enum('status_pembayaran', ['belum_dibayar', 'sudah_dibayar'])->default('belum_dibayar');
            $table->enum('status', [
                'menunggu',
                'diproses',
                'siap_diambil',
                'sedang_diantar',
                'selesai',
                'dibatalkan'
            ])->default('menunggu')->index();
            $table->decimal('total_harga', 12, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        Schema::create('saldo_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->enum('tipe', ['topup', 'pembayaran', 'refund'])->index();
            $table->decimal('jumlah', 12, 2);
            $table->decimal('saldo_sebelum', 12, 2);
            $table->decimal('saldo_sesudah', 12, 2);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldo_transactions');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['saldo', 'is_active']);
        });
        Schema::dropIfExists('menus');
        Schema::dropIfExists('kategori_menus');
    }
};
