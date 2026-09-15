<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentConfirmation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentConfirmationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $payments = PaymentConfirmation::with(['order.user', 'order.package'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return response()->json($payments);
    }

    public function store(Request $request, Order $order): JsonResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        $data = $request->validate([
            'bank_id' => ['required', 'exists:banks,id'],
            'nominal' => ['required', 'numeric'],
            'bank_pengirim' => ['required', 'string'],
            'nomor_rekening_pengirim' => ['required', 'string'],
            'bukti_transfer' => ['required', 'image', 'max:4096'],
        ]);

        $data['bukti_transfer'] = $request->file('bukti_transfer')->store('payments', 'public');
        $data['order_id'] = $order->id;
        $data['status'] = 'pending';

        $payment = PaymentConfirmation::updateOrCreate(['order_id' => $order->id], $data);
        $order->update(['status' => 'waiting_confirmation']);

        return response()->json(['data' => $payment], 201);
    }

    public function approve(PaymentConfirmation $payment): JsonResponse
    {
        $payment->update(['status' => 'approved']);
        $payment->order->update(['status' => 'complete']);

        return response()->json(['message' => 'Pembayaran disetujui.']);
    }
}
