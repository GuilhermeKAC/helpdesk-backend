<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    private const CACHE_TTL = 300; // 5 minutes

    public function getStats(User $user): array
    {
        $cacheKey = "dashboard:stats:{$user->role->value}:{$user->id}";

        return Cache::remember($cacheKey, self::CACHE_TTL, fn () => $this->buildStats($user));
    }

    public function getCharts(User $user): array
    {
        $cacheKey = "dashboard:charts:{$user->role->value}:{$user->id}";

        return Cache::remember($cacheKey, self::CACHE_TTL, fn () => $this->buildCharts($user));
    }

    public function flushForUser(User $user): void
    {
        Cache::forget("dashboard:stats:{$user->role->value}:{$user->id}");
        Cache::forget("dashboard:charts:{$user->role->value}:{$user->id}");
    }

    private function buildStats(User $user): array
    {
        $query = $this->baseQuery($user);

        $byStatus = (clone $query)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $overdue = (clone $query)
            ->whereNotIn('status', [TicketStatus::RESOLVED->value, TicketStatus::CLOSED->value])
            ->where('due_date', '<', now())
            ->count();

        $stats = [
            'total' => array_sum($byStatus),
            'by_status' => $byStatus,
            'overdue' => $overdue,
        ];

        if ($user->role !== UserRole::CUSTOMER) {
            $stats['avg_resolution_time'] = (clone $query)
                ->whereNotNull('resolution_time')
                ->avg('resolution_time');

            $stats['avg_response_time'] = (clone $query)
                ->whereNotNull('response_time')
                ->avg('response_time');

            $stats['by_priority'] = (clone $query)
                ->select('priority', DB::raw('count(*) as total'))
                ->groupBy('priority')
                ->pluck('total', 'priority')
                ->toArray();
        }

        return $stats;
    }

    private function buildCharts(User $user): array
    {
        $query = $this->baseQuery($user);

        $ticketsPerDay = (clone $query)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => ['date' => $row->date, 'total' => $row->total]);

        $charts = ['tickets_per_day' => $ticketsPerDay];

        if ($user->role !== UserRole::CUSTOMER) {
            $charts['by_category'] = (clone $query)
                ->select('category_id', DB::raw('count(*) as total'))
                ->with('category:id,name,color')
                ->groupBy('category_id')
                ->get()
                ->map(fn ($row) => [
                    'category' => $row->category?->name,
                    'color' => $row->category?->color,
                    'total' => $row->total,
                ]);
        }

        return $charts;
    }

    private function baseQuery(User $user)
    {
        $query = Ticket::query();

        if ($user->role === UserRole::CUSTOMER) {
            $query->forUser($user->id);
        } elseif ($user->role === UserRole::TECHNICIAN) {
            $query->forTechnician($user->id);
        }

        return $query;
    }
}
