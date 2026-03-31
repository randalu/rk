<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use App\Models\Salesperson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role', 'salesperson')->latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = UserRole::orderBy('name')->get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:8|confirmed',
            'role_id'               => 'required|exists:user_roles,id',
            'is_salesperson'        => 'nullable|boolean',
            'salesperson_name'      => 'nullable|string|max:255',
            'salesperson_phone'     => 'nullable|string|max:20',
            'commission_type'       => 'nullable|in:qty_based,value_based',
            'target_period'         => 'nullable|in:monthly,quarterly,yearly',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id,
        ]);

        // If this user is a salesperson, create the salesperson record
        if ($request->is_salesperson) {
            Salesperson::create([
                'user_id'         => $user->id,
                'name'            => $request->salesperson_name ?? $request->name,
                'phone'           => $request->salesperson_phone,
                'commission_type' => $request->commission_type ?? 'value_based',
                'target_period'   => $request->target_period   ?? 'monthly',
                'is_active'       => true,
            ]);
        }

        return redirect()->route('users.index')
                         ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load('role', 'salesperson');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = UserRole::orderBy('name')->get();
        $user->load('salesperson');
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email,' . $user->id,
            'password'          => 'nullable|min:8|confirmed',
            'role_id'           => 'required|exists:user_roles,id',
            'salesperson_name'  => 'nullable|string|max:255',
            'salesperson_phone' => 'nullable|string|max:20',
            'commission_type'   => 'nullable|in:qty_based,value_based',
            'target_period'     => 'nullable|in:monthly,quarterly,yearly',
        ]);

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Update or create salesperson record
        if ($request->is_salesperson) {
            $user->salesperson()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name'            => $request->salesperson_name ?? $user->name,
                    'phone'           => $request->salesperson_phone,
                    'commission_type' => $request->commission_type ?? 'value_based',
                    'target_period'   => $request->target_period   ?? 'monthly',
                    'is_active'       => true,
                ]
            );
        }

        return redirect()->route('users.index')
                         ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                             ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
                         ->with('success', 'User deleted.');
    }
}