<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\HotelResource;
use App\Models\Hotel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HotelController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => HotelResource::collection(Hotel::all())]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string'],
            'lokasi' => ['nullable', 'string'],
            'keterangan' => ['nullable', 'string'],
        ]);
        $data['slug'] = Str::slug($data['nama']);
        $hotel = Hotel::create($data);

        return response()->json(['data' => new HotelResource($hotel)], 201);
    }

    public function update(Request $request, Hotel $hotel): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['sometimes', 'string'],
            'lokasi' => ['nullable', 'string'],
            'keterangan' => ['nullable', 'string'],
        ]);
        $hotel->update($data);

        return response()->json(['data' => new HotelResource($hotel)]);
    }

    public function destroy(Hotel $hotel): JsonResponse
    {
        $hotel->delete();

        return response()->json(['message' => 'Hotel dihapus.']);
    }
}
