<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderParticipant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales(Request $request): JsonResponse
    {
        $query = Order::with(['package', 'user'])
            ->where('status', 'complete');

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->latest()->paginate(15);
        $totalRevenue = $query->sum('total');

        return response()->json([
            'data' => $orders,
            'total_revenue' => $totalRevenue,
        ]);
    }

    public function agents(Request $request): JsonResponse
    {
        $agents = User::role('merchants')
            ->withCount('packages')
            ->with(['orders' => fn ($q) => $q->where('status', 'complete')])
            ->get()
            ->map(fn ($agent) => [
                'id' => $agent->id,
                'name' => $agent->name,
                'business_name' => $agent->business_name,
                'packages_count' => $agent->packages_count,
                'total_orders' => $agent->orders->count(),
                'total_revenue' => $agent->orders->sum('total'),
            ]);

        return response()->json(['data' => $agents]);
    }

    public function exportParticipants(Request $request)
    {
        // Simple CSV export without maatwebsite/excel for now
        $query = OrderParticipant::with(['order.user', 'order.package']);

        if ($request->filled('package_id')) {
            $query->whereHas('order', fn ($q) => $q->where('umrah_package_id', $request->package_id));
        }

        $participants = $query->get();

        $csv = "Nama,Nomor Ponsel,Tipe,Nomor Order,Paket,Customer\n";
        foreach ($participants as $p) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s\n",
                $p->nama,
                $p->nomor_ponsel ?? '-',
                $p->tipe,
                $p->order?->order_code ?? '-',
                $p->order?->package?->title ?? '-',
                $p->order?->user?->name ?? '-'
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="jamaah-export.csv"',
        ]);
    }
}
