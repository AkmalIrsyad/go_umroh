<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BankResource;
use App\Models\Bank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => BankResource::collection(Bank::all())]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string'],
            'nama_akun' => ['required', 'string'],
            'nomor_rekening' => ['required', 'string'],
        ]);
        $bank = Bank::create($data);

        return response()->json(['data' => new BankResource($bank)], 201);
    }

    public function update(Request $request, Bank $bank): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['sometimes', 'string'],
            'nama_akun' => ['sometimes', 'string'],
            'nomor_rekening' => ['sometimes', 'string'],
        ]);
        $bank->update($data);

        return response()->json(['data' => new BankResource($bank)]);
    }

    public function destroy(Bank $bank): JsonResponse
    {
        $bank->delete();

        return response()->json(['message' => 'Bank dihapus.']);
    }
}
