<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderParticipant;
use App\Models\UmrahPackage;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createOrder(array $data, User $user): Order
    {
        /** @var UmrahPackage $package */
        $package = UmrahPackage::findOrFail($data['umrah_package_id']);

        $kodeUnik = rand(100, 999);
        $total = ($package->price * count($data['participants'])) + $package->booking_fee + $kodeUnik;

        /** @var Order $order */
        $order = Order::create([
            'order_code' => 'ORD-'.strtoupper(Str::random(8)),
            'user_id' => $user->id,
            'umrah_package_id' => $package->id,
            'total' => $total,
            'kode_unik' => $kodeUnik,
            'status' => 'waiting_payment',
            'travel_agent_id' => $package->user_id,
        ]);

        foreach ($data['participants'] as $participant) {
            $order->participants()->create([
                'nama' => $participant['nama'],
                'nomor_ponsel' => $participant['nomor_ponsel'] ?? null,
                'tipe' => $participant['tipe'] ?? 'dewasa',
            ]);
        }

        $package->increment('booked_count', count($data['participants']));

        return $order->load(['package', 'participants', 'paymentConfirmation']);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getAdminOrders(array $filters): LengthAwarePaginator
    {
        $query = Order::with(['user', 'package', 'paymentConfirmation']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['search'])) {
            $query->where('order_code', 'like', '%'.$filters['search'].'%')
                ->orWhereHas('user', fn ($q) => $q->where('name', 'like', '%'.$filters['search'].'%'));
        }

        return $query->latest()->paginate(15);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getMyOrders(User $user, array $filters): LengthAwarePaginator
    {
        return Order::with(['package.images' => fn ($q) => $q->where('is_primary', true), 'paymentConfirmation'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getAllParticipants(array $filters): LengthAwarePaginator
    {
        $query = OrderParticipant::with(['order.user', 'order.package']);

        if (! empty($filters['package_id'])) {
            $query->whereHas('order', fn ($q) => $q->where('umrah_package_id', $filters['package_id']));
        }
        if (! empty($filters['search'])) {
            $query->where('nama', 'like', '%'.$filters['search'].'%');
        }

        return $query->latest()->paginate(15);
    }
}
