<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DomainOrder;
use App\Services\Domain\DomainOrderStateMachine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Internal tick endpoint — called by dashboard JS polling while an admin/client has
 * the domain management page open, and by Retry buttons.
 * No cron, no queue workers, no scheduler.
 */
class DomainTickController extends Controller
{
    public function __construct(private DomainOrderStateMachine $stateMachine) {}

    /**
     * POST /api/domains/{order}/tick
     * Advances a domain order by one step. Idempotent.
     */
    public function tick(Request $request, DomainOrder $order): JsonResponse
    {
        $this->stateMachine->tick($order);
        $order->refresh();

        return response()->json([
            'state'      => $order->state,
            'last_error' => $order->last_error,
        ]);
    }

    /**
     * POST /api/domains/{order}/retry
     * Admin action after funding the GO54 wallet.
     */
    public function retry(Request $request, DomainOrder $order): JsonResponse
    {
        $this->stateMachine->retryFromAwaitingWallet($order);
        $order->refresh();

        return response()->json([
            'state'      => $order->state,
            'last_error' => $order->last_error,
        ]);
    }
}
