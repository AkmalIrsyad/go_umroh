<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index(): JsonResponse
    {
        $sliders = Slider::orderBy('order_position')->get()->map(fn ($s) => [
            'id' => $s->id,
            'image' => asset('storage/'.$s->image),
            'order_position' => $s->order_position,
        ]);

        return response()->json(['data' => $sliders]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(['image' => ['required', 'image', 'max:4096']]);
        $path = $request->file('image')->store('sliders', 'public');
        $slider = Slider::create(['image' => $path, 'order_position' => Slider::max('order_position') + 1]);

        return response()->json(['data' => ['id' => $slider->id, 'image' => asset('storage/'.$slider->image)]], 201);
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate(['order' => ['required', 'array'], 'order.*' => ['integer']]);
        foreach ($request->order as $position => $id) {
            Slider::where('id', $id)->update(['order_position' => $position]);
        }

        return response()->json(['message' => 'Urutan slider diperbarui.']);
    }

    public function destroy(Slider $slider): JsonResponse
    {
        $slider->delete();

        return response()->json(['message' => 'Slider dihapus.']);
    }
}
