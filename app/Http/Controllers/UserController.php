<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            
            if (isset($validated['roles'])) {
                $user->roles()->sync($validated['roles']);
            }

            // For AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User created successfully',
                    'user' => $user->load('roles')
                ]);
            }

            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());
            
            // For AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create user',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()->with('error', 'Failed to create user. ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        $user->load('roles');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ];
        
        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        $validated = $request->validate($rules);

        try {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];
            
            // Only update password if it's provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($validated['password']);
            }
            
            $user->update($userData);
            
            if (isset($validated['roles'])) {
                $user->roles()->sync($validated['roles']);
            } else {
                $user->roles()->detach();
            }

            return redirect()->route('users.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            Log::error('User update failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update user. ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->roles()->detach();
            $user->delete();
            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            Log::error('User deletion failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete user. ' . $e->getMessage());
        }
    }
} 