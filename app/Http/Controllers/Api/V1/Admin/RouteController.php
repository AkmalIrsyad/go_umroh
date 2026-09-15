<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RouteResource;
use App\Models\Route;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RouteController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => RouteResource::collection(Route::all())]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string']]);
        $data['slug'] = Str::slug($data['name']);
        $route = Route::create($data);

        return response()->json(['data' => new RouteResource($route)], 201);
    }

    public function update(Request $request, Route $route): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string']]);
        $data['slug'] = Str::slug($data['name']);
        $route->update($data);

        return response()->json(['data' => new RouteResource($route)]);
    }

    public function destroy(Route $route): JsonResponse
    {
        $route->delete();

        return response()->json(['message' => 'Rute dihapus.']);
    }
}
