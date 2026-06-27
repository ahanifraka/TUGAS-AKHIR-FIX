<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $sortField = $request->query('sortField');
        $sortOrder = (int) $request->query('sortOrder', 1); // PrimeVue: 1 asc, -1 desc

        $query = User::query();
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('full_name', 'like', '%' . $search . '%');
            });
        }

        // Apply sorting if requested (whitelist allowed fields)
        $allowedSorts = ['name', 'email', 'full_name', 'created_at', 'updated_at'];
        if ($sortField && in_array($sortField, $allowedSorts, true)) {
            $direction = $sortOrder === 1 ? 'asc' : 'desc';
            $query->orderBy($sortField, $direction);
        } else {
            // Default sort
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate(10)->withQueryString();

        $data = [
            'data' => $users->getCollection()->transform(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'full_name' => $u->full_name,
                    'email' => $u->email,
                    'role' => $u->getRoleNames()->implode(', '),
                    'updated_at' => $u->updated_at?->toDateTimeString(),
                ];
            }),
            'links' => [
                'first' => $users->url(1),
                'last' => $users->url($users->lastPage()),
                'prev' => $users->previousPageUrl(),
                'next' => $users->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ];

        return Inertia::render('Users/Index', [
            'users' => $data,
            'filters' => [
                'search' => $search,
                'sortField' => $sortField,
                'sortOrder' => $sortOrder,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Users/Create', [
            'roles' => Role::pluck('name'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'password' => ['required', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Created user: ' . $user->name . ' (' . $user->email . ')',
        ]);

        return redirect()->route('users.index')->with('status', 'User created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return Inertia::render('Users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'full_name' => $user->fullname,
                'email' => $user->email,
                'created_at' => $user->created_at?->toDateTimeString(),
                'updated_at' => $user->updated_at?->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'full_name' => $user->full_name,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed'],
        ]);

        $update = [
            'name' => $validated['name'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $update['password'] = Hash::make($validated['password']);
        }

        $user->update($update);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Updated user: ' . $user->name . ' (' . $user->email . ')',
        ]);

        return redirect()->route('users.index')->with('status', 'User updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $userName = $user->name;
        $userEmail = $user->email;
        $user->delete();
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Deleted user: ' . $userName . ' (' . $userEmail . ')',
        ]);
        
        return redirect()->route('users.index')->with('status', 'User deleted.');
    }
}