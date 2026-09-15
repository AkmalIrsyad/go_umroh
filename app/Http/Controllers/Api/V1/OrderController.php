<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder($request->validated(), $request->user());

        return response()->json(['data' => new OrderResource($order)], 201);
    }

    public function myOrders(Request $request): JsonResponse
    {
        $orders = $this->orderService->getMyOrders($request->user(), $request->all());

        return response()->json(OrderResource::collection($orders)->response()->getData(true));
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        return response()->json(['data' => new OrderResource($order->load(['package', 'participants', 'paymentConfirmation']))]);
    }

    public function adminIndex(Request $request): JsonResponse
    {
        $orders = $this->orderService->getAdminOrders($request->all());

        return response()->json(OrderResource::collection($orders)->response()->getData(true));
    }

    public function adminShow(Order $order): JsonResponse
    {
        return response()->json(['data' => new OrderResource($order->load(['user', 'package', 'participants', 'paymentConfirmation']))]);
    }

    public function participants(Request $request): JsonResponse
    {
        $participants = $this->orderService->getAllParticipants($request->all());

        return response()->json($participants);
    }
}
