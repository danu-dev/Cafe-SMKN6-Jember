<?php

namespace Database\Seeders;

use App\Models\KategoriMenu;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@cafe.test'],
            [
                'name' => 'Admin Cafe',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'saldo' => 0,
                'is_active' => true,
            ]
        );

        // Kurir
        User::firstOrCreate(
            ['email' => 'kurir1@cafe.test'],
            [
                'name' => 'Budi Santoso',
                'username' => 'kurir1',
                'password' => Hash::make('password'),
                'role' => 'kurir',
                'saldo' => 0,
                'is_active' => true,
            ]
        );

        // Siswa
        User::firstOrCreate(
            ['email' => 'siswa1@cafe.test'],
            [
                'name' => 'Ahmad Fauzi',
                'username' => 'siswa1',
                'password' => Hash::make('password'),
                'kelas' => '10',
                'role' => 'siswa',
                'saldo' => 50000,
                'is_active' => true,
            ]
        );

        // Kategori Menu
        $makanan = KategoriMenu::firstOrCreate(['slug' => 'makanan'], ['nama' => 'Makanan']);
        $minuman = KategoriMenu::firstOrCreate(['slug' => 'minuman'], ['nama' => 'Minuman']);
        $snack = KategoriMenu::firstOrCreate(['slug' => 'snack'], ['nama' => 'Snack']);

        // Sample Menu
        Menu::firstOrCreate(['nama' => 'Nasi Goreng Spesial'], [
            'kategori_id' => $makanan->id,
            'deskripsi' => 'Nasi goreng dengan telur, suwiran ayam, dan kerupuk.',
            'harga' => 15000,
            'is_available' => true,
        ]);

        Menu::firstOrCreate(['nama' => 'Es Teh Manis'], [
            'kategori_id' => $minuman->id,
            'deskripsi' => 'Teh manis dingin segar.',
            'harga' => 4000,
            'is_available' => true,
        ]);

        Menu::firstOrCreate(['nama' => 'Roti Bakar Coklat Keju'], [
            'kategori_id' => $snack->id,
            'deskripsi' => 'Roti bakar isi coklat lumer dengan parutan keju gurih.',
            'harga' => 10000,
            'is_available' => true,
        ]);
    }
}
