<?php

namespace App\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'in:regular,vip,promo'],
            'departure_date' => ['sometimes', 'date'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'booking_fee' => ['nullable', 'numeric', 'min:0'],
            'duration' => ['sometimes', 'integer', 'min:1'],
            'capacity' => ['sometimes', 'integer', 'min:1'],
            'details' => ['nullable', 'string'],
            'requirement' => ['nullable', 'string'],
            'air_line_id' => ['sometimes', 'exists:air_lines,id'],
            'route_id' => ['sometimes', 'exists:routes,id'],
            'hotel_ids' => ['nullable', 'array'],
            'hotel_ids.*' => ['exists:hotels,id'],
        ];
    }
}
