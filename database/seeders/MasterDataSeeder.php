<?php

namespace Database\Seeders;

use App\Models\AirLine;
use App\Models\Bank;
use App\Models\Hotel;
use App\Models\Route;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Banks
        Bank::create(['nama' => 'BCA', 'nama_akun' => 'PT Go Umrah', 'nomor_rekening' => '1234567890']);
        Bank::create(['nama' => 'Mandiri', 'nama_akun' => 'PT Go Umrah', 'nomor_rekening' => '0987654321']);
        Bank::create(['nama' => 'BSI', 'nama_akun' => 'PT Go Umrah', 'nomor_rekening' => '1122334455']);

        // AirLines
        AirLine::create(['nama' => 'Garuda Indonesia', 'slug' => 'garuda-indonesia', 'keterangan' => 'Direct Flight Jakarta - Jeddah']);
        AirLine::create(['nama' => 'Saudia Airlines', 'slug' => 'saudia-airlines', 'keterangan' => 'Direct Flight Jakarta - Madinah']);
        AirLine::create(['nama' => 'Emirates', 'slug' => 'emirates', 'keterangan' => 'Transit via Dubai']);

        // Hotels
        Hotel::create(['nama' => 'Pullman Zamzam Makkah', 'slug' => 'pullman-zamzam-makkah', 'lokasi' => 'Makkah', 'keterangan' => 'Bintang 5, dekat Masjidil Haram']);
        Hotel::create(['nama' => 'Anjum Hotel', 'slug' => 'anjum-hotel', 'lokasi' => 'Makkah', 'keterangan' => 'Bintang 5']);
        Hotel::create(['nama' => 'Movenpick Hotel', 'slug' => 'movenpick-hotel', 'lokasi' => 'Madinah', 'keterangan' => 'Bintang 5, dekat Masjid Nabawi']);
        Hotel::create(['nama' => 'Dallah Taibah', 'slug' => 'dallah-taibah', 'lokasi' => 'Madinah', 'keterangan' => 'Bintang 4']);

        // Routes
        Route::create(['name' => 'Jakarta - Jeddah - Makkah - Madinah - Jakarta', 'slug' => 'cgk-jed-mak-mad-cgk']);
        Route::create(['name' => 'Jakarta - Madinah - Makkah - Jeddah - Jakarta', 'slug' => 'cgk-mad-mak-jed-cgk']);
    }
}
