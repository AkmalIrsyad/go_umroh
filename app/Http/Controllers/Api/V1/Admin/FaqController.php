<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqGroupResource;
use App\Models\Faq;
use App\Models\FaqGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function publicIndex(): JsonResponse
    {
        return response()->json(['data' => FaqGroupResource::collection(FaqGroup::with('faqs')->get())]);
    }

    public function index(): JsonResponse
    {
        return response()->json(['data' => FaqGroupResource::collection(FaqGroup::with('faqs')->get())]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string'],
            'faqs' => ['array'],
            'faqs.*.title' => ['required', 'string'],
            'faqs.*.content' => ['required', 'string'],
        ]);
        $group = FaqGroup::create(['title' => $data['title']]);
        foreach ($data['faqs'] ?? [] as $faq) {
            $group->faqs()->create($faq);
        }

        return response()->json(['data' => new FaqGroupResource($group->load('faqs'))], 201);
    }

    public function update(Request $request, FaqGroup $faqGroup): JsonResponse
    {
        $data = $request->validate(['title' => ['required', 'string']]);
        $faqGroup->update($data);

        return response()->json(['data' => new FaqGroupResource($faqGroup->load('faqs'))]);
    }

    public function destroy(FaqGroup $faqGroup): JsonResponse
    {
        $faqGroup->delete();

        return response()->json(['message' => 'FAQ Group dihapus.']);
    }

    public function show(FaqGroup $faqGroup): JsonResponse
    {
        return response()->json(['data' => new FaqGroupResource($faqGroup->load('faqs'))]);
    }

    public function storeFaq(Request $request, FaqGroup $faqGroup): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
        ]);

        $faq = $faqGroup->faqs()->create($data);

        return response()->json(['message' => 'FAQ ditambahkan.', 'data' => $faq], 201);
    }

    public function updateFaq(Request $request, Faq $faq): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string'],
            'content' => ['sometimes', 'string'],
        ]);

        $faq->update($data);

        return response()->json(['message' => 'FAQ diperbarui.', 'data' => $faq]);
    }

    public function destroyFaq(Faq $faq): JsonResponse
    {
        $faq->delete();

        return response()->json(['message' => 'FAQ dihapus.']);
    }
}
