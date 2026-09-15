<?php

namespace App\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:regular,vip,promo'],
            'departure_date' => ['required', 'date', 'after:today'],
            'price' => ['required', 'numeric', 'min:0'],
            'booking_fee' => ['nullable', 'numeric', 'min:0'],
            'duration' => ['required', 'integer', 'min:1'],
            'capacity' => ['required', 'integer', 'min:1'],
            'details' => ['nullable', 'string'],
            'requirement' => ['nullable', 'string'],
            'air_line_id' => ['required', 'exists:air_lines,id'],
            'route_id' => ['required', 'exists:routes,id'],
            'hotel_ids' => ['nullable', 'array'],
            'hotel_ids.*' => ['exists:hotels,id'],
        ];
    }
}
