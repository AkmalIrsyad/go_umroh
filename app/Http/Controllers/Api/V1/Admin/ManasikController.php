<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ManasikGroupResource;
use App\Models\Manasik;
use App\Models\ManasikGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManasikController extends Controller
{
    public function publicIndex(): JsonResponse
    {
        return response()->json(['data' => ManasikGroupResource::collection(ManasikGroup::with('manasiks')->get())]);
    }

    public function index(): JsonResponse
    {
        return response()->json(['data' => ManasikGroupResource::collection(ManasikGroup::with('manasiks')->get())]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string'],
            'manasiks' => ['array'],
            'manasiks.*.title' => ['required', 'string'],
            'manasiks.*.content' => ['required', 'string'],
        ]);
        $group = ManasikGroup::create(['title' => $data['title']]);
        foreach ($data['manasiks'] ?? [] as $item) {
            $group->manasiks()->create($item);
        }

        return response()->json(['data' => new ManasikGroupResource($group->load('manasiks'))], 201);
    }

    public function update(Request $request, ManasikGroup $manasikGroup): JsonResponse
    {
        $manasikGroup->update($request->validate(['title' => ['required', 'string']]));

        return response()->json(['data' => new ManasikGroupResource($manasikGroup->load('manasiks'))]);
    }

    public function destroy(ManasikGroup $manasikGroup): JsonResponse
    {
        $manasikGroup->delete();

        return response()->json(['message' => 'Manasik Group dihapus.']);
    }

    public function show(ManasikGroup $manasikGroup): JsonResponse
    {
        return response()->json(['data' => new ManasikGroupResource($manasikGroup->load('manasiks'))]);
    }

    public function storeManasik(Request $request, ManasikGroup $manasikGroup): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
        ]);

        $item = $manasikGroup->manasiks()->create($data);

        return response()->json(['message' => 'Materi manasik ditambahkan.', 'data' => $item], 201);
    }

    public function updateManasik(Request $request, Manasik $manasik): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string'],
            'content' => ['sometimes', 'string'],
        ]);

        $manasik->update($data);

        return response()->json(['message' => 'Materi manasik diperbarui.', 'data' => $manasik]);
    }

    public function destroyManasik(Manasik $manasik): JsonResponse
    {
        $manasik->delete();

        return response()->json(['message' => 'Materi manasik dihapus.']);
    }
}
