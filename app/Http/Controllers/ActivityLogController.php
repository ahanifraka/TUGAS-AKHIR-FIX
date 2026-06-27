<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $role = $request->query('role');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $perPage = (int) $request->query('per_page', 20);
        $allowedPerPage = [10, 20, 50];
        if (!in_array($perPage, $allowedPerPage, true)) {
            $perPage = 20;
        }

        $query = ActivityLog::query()->with('user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('activity', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($role) {
            $query->whereHas('user', function ($q) use ($role) {
                $q->role($role);
            });
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $logs = $query->latest('created_at')->paginate($perPage)->withQueryString();

        $data = [
            'data' => $logs->getCollection()->transform(function ($log) {
                return [
                    'id' => $log->id,
                    'user_id' => $log->user_id,
                    'user_name' => optional($log->user)->name,
                    'user_roles' => optional($log->user)->getRoleNames(),
                    'activity' => $log->activity,
                    'created_at' => $log->created_at?->toDateTimeString(),
                ];
            }),
            'links' => [
                'first' => $logs->url(1),
                'last' => $logs->url($logs->lastPage()),
                'prev' => $logs->previousPageUrl(),
                'next' => $logs->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ],
        ];

        $roles = \Spatie\Permission\Models\Role::pluck('name');

        return Inertia::render('Log/Index', [
            'logs' => $data,
            'roles' => $roles,
            'filters' => [
                'search' => $search,
                'role' => $role,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'per_page' => $perPage,
            ],
        ]);
    }
}