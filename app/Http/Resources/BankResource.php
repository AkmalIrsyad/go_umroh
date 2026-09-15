<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'nama_akun' => $this->nama_akun,
            'nomor_rekening' => $this->nomor_rekening,
            'logo' => $this->logo ? asset('storage/'.$this->logo) : null,
        ];
    }
}
