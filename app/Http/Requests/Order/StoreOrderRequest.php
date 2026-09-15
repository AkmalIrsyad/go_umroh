<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'umrah_package_id' => ['required', 'exists:umrah_packages,id'],
            'participants' => ['required', 'array', 'min:1'],
            'participants.*.nama' => ['required', 'string', 'max:255'],
            'participants.*.nomor_ponsel' => ['nullable', 'string', 'max:20'],
            'participants.*.tipe' => ['required', 'in:dewasa,anak'],
        ];
    }
}
