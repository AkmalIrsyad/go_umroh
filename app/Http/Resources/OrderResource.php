<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_code' => $this->order_code,
            'total' => $this->total,
            'kode_unik' => $this->kode_unik,
            'status' => $this->status,
            'package' => new PackageResource($this->whenLoaded('package')),
            'user' => new UserResource($this->whenLoaded('user')),
            'participants' => $this->whenLoaded('participants', fn () => $this->participants->map(fn ($p) => [
                'id' => $p->id,
                'nama' => $p->nama,
                'nomor_ponsel' => $p->nomor_ponsel,
                'tipe' => $p->tipe,
            ])),
            'payment' => $this->whenLoaded('paymentConfirmation', fn () => $this->paymentConfirmation ? [
                'id' => $this->paymentConfirmation->id,
                'nominal' => $this->paymentConfirmation->nominal,
                'status' => $this->paymentConfirmation->status,
                'bukti_transfer' => $this->paymentConfirmation->bukti_transfer
                    ? asset('storage/'.$this->paymentConfirmation->bukti_transfer) : null,
            ] : null),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
