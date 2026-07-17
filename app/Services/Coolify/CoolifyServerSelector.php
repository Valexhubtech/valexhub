<?php

namespace App\Services\Coolify;

use Illuminate\Support\Facades\Log;
use Wave\CoolifyServer;

class CoolifyServerSelector
{
    /**
     * Find the first active server with remaining capacity, ordered by priority.
     * Returns null when all servers are full or none are active.
     */
    public function findAvailableServer(): ?CoolifyServer
    {
        $servers = CoolifyServer::active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($servers->isEmpty()) {
            Log::warning('CoolifyServerSelector: no active Coolify servers configured.');

            return null;
        }

        foreach ($servers as $server) {
            if (! $server->isFull()) {
                return $server;
            }
        }

        // All servers are at capacity
        Log::critical('CoolifyServerSelector: all Coolify servers are at maximum capacity.', [
            'server_count' => $servers->count(),
        ]);

        return null;
    }

    /**
     * Summary of current server capacity for dashboard / notifications.
     */
    public function capacitySummary(): array
    {
        $servers = CoolifyServer::active()->get();

        $totalSlots = $servers->sum('max_deployments');
        $usedSlots = $servers->sum(fn ($s) => $s->used_slots);

        return [
            'active_servers' => $servers->count(),
            'total_slots' => $totalSlots,
            'used_slots' => $usedSlots,
            'available_slots' => max(0, $totalSlots - $usedSlots),
            'all_full' => $usedSlots >= $totalSlots && $totalSlots > 0,
        ];
    }
}
