<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'type' => $this->type,
            'departure_date' => $this->departure_date instanceof \DateTimeInterface ? $this->departure_date->format('Y-m-d') : ($this->departure_date ? (string) $this->departure_date : null),
            'price' => $this->price,
            'booking_fee' => $this->booking_fee,
            'duration' => $this->duration,
            'capacity' => $this->capacity,
            'booked_count' => $this->booked_count,
            'available_seats' => max(0, $this->capacity - $this->booked_count),
            'details' => $this->details,
            'requirement' => $this->requirement,
            'status' => $this->status,
            'airline' => new AirLineResource($this->whenLoaded('airLine')),
            'route' => new RouteResource($this->whenLoaded('route')),
            'hotels' => HotelResource::collection($this->whenLoaded('hotels')),
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($img) => [
                'id' => $img->id,
                'url' => asset('storage/'.$img->path),
                'is_primary' => $img->is_primary,
            ])),
            'merchant' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'business_name' => $this->user->business_name,
            ]),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
