<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Default initial settings
        $defaults = [
            'store_name' => 'Cafe Kantin Sekolah',
            'store_description' => 'Pesan makanan & minuman kantin sekolah dengan mudah dan cepat.',
            'is_open' => '1',
            'open_time' => '07:00',
            'close_time' => '15:00',
            'whatsapp_contact' => '081234567890',
            'auto_accept_orders' => '0',
        ];

        foreach ($defaults as $key => $value) {
            DB::table('store_settings')->insert([
                'key' => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
