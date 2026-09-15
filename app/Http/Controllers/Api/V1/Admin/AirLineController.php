<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AirLineResource;
use App\Models\AirLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AirLineController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => AirLineResource::collection(AirLine::all())]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string'],
            'keterangan' => ['nullable', 'string'],
        ]);
        $data['slug'] = Str::slug($data['nama']);
        $airline = AirLine::create($data);

        return response()->json(['data' => new AirLineResource($airline)], 201);
    }

    public function update(Request $request, AirLine $airline): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['sometimes', 'string'],
            'keterangan' => ['nullable', 'string'],
        ]);
        if (isset($data['nama'])) {
            $data['slug'] = Str::slug($data['nama']);
        }
        $airline->update($data);

        return response()->json(['data' => new AirLineResource($airline)]);
    }

    public function destroy(AirLine $airline): JsonResponse
    {
        $airline->delete();

        return response()->json(['message' => 'Maskapai dihapus.']);
    }
}
