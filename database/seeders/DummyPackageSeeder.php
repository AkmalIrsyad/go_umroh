<?php

namespace Database\Seeders;

use App\Models\AirLine;
use App\Models\Hotel;
use App\Models\Route;
use App\Models\UmrahPackage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummyPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchant = User::role('merchants')->first();
        $garuda = AirLine::where('slug', 'garuda-indonesia')->first();
        $route = Route::first();
        $pullman = Hotel::where('slug', 'pullman-zamzam-makkah')->first();
        $movenpick = Hotel::where('slug', 'movenpick-hotel')->first();

        if ($merchant && $garuda && $route) {
            $package = UmrahPackage::create([
                'title' => 'Paket Umrah Plus Turki 12 Hari',
                'type' => 'vip',
                'departure_date' => Carbon::now()->addMonths(2)->toDateString(),
                'price' => 35000000,
                'booking_fee' => 5000000,
                'duration' => 12,
                'capacity' => 45,
                'details' => 'Paket Umrah VIP dengan fasilitas hotel bintang 5 dan city tour Turki.',
                'requirement' => 'Paspor dengan masa berlaku minimal 6 bulan, Pas foto 4x6.',
                'status' => 'approved',
                'air_line_id' => $garuda->id,
                'route_id' => $route->id,
                'user_id' => $merchant->id,
            ]);

            // Attach hotels
            if ($pullman) {
                $package->hotels()->attach($pullman->id);
            }
            if ($movenpick) {
                $package->hotels()->attach($movenpick->id);
            }
        }
    }
}
