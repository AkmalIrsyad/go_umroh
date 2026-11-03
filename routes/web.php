<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Models\UmrahPackage;
use Illuminate\Support\Facades\Route;

Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');

// Open Graph & SEO for each individual package (SSR for social media previews and search bots)
Route::get('/packages/{slug}', function (string $slug) {
    $seo = null;

    try {
        $package = UmrahPackage::with(['airLine', 'route', 'hotels', 'images', 'user'])
            ->where('slug', $slug)
            ->first();

        if ($package) {
            $primaryImage = $package->images->where('is_primary', true)->first()
                ?? $package->images->first();

            $imageUrl = $primaryImage
                ? asset('storage/' . $primaryImage->path)
                : asset('go_umroh.svg');

            $priceFormatted = 'Rp ' . number_format((float) $package->price, 0, ',', '.');
            $dateFormatted = $package->departure_date ? $package->departure_date->format('d M Y') : '-';
            $airlineName = $package->airLine?->nama ?? 'Penerbangan Pilihan';
            $availableSeats = max(0, $package->capacity - $package->booked_count);

            $description = 'Paket Umrah ' . strtoupper($package->type ?? 'Reguler') . " {$package->duration} Hari bersama {$airlineName}. Keberangkatan: {$dateFormatted}. Harga mulai {$priceFormatted}. Sisa {$availableSeats} kursi.";

            $seo = [
                'title' => "{$package->title} — Go Umroh",
                'description' => $description,
                'image' => $imageUrl,
                'url' => url('/packages/' . $package->slug),
                'type' => 'product',
                'price' => $package->price,
                'package' => [
                    'name' => $package->title,
                    'price' => (float) $package->price,
                    'currency' => 'IDR',
                    'seats' => $availableSeats,
                    'departure' => $dateFormatted,
                    'duration' => $package->duration,
                    'airline' => $airlineName,
                    'merchant' => $package->user?->business_name ?? $package->user?->name ?? 'Go Umroh',
                ],
            ];
        }
    } catch (\Throwable $e) {
        // Fallback to default SPA view if database is temporarily unavailable
    }

    return view('welcome', compact('seo'));
});

// Serve the SPA for all routes — Vue Router handles navigation on the client.
Route::get('/{any?}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$');
