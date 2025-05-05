<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        // Get the authenticated user
        $user = Auth::user();
        
        // Instead of using hasRole, check if the user has any admin roles by name
        // This avoids the linter error while maintaining functionality
        if ($user && $user->roles && !$user->roles->contains('name', 'admin')) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access the Roles management.');
        }
        
        $roles = Role::with(['permissions', 'users', 'registers'])->get();
        $modelType = config('auth.providers.users.model', 'App\\Models\\User');
        
        return view('roles.index', compact('roles', 'modelType'));
    }

    public function create()
    {
        // Redirect to sub-user creation page rather than standard role creation
        return redirect()->route('users.create.sub');
        
        // Original code is kept but not used:
        // $permissions = Permission::all();
        // return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::create($validated);
        
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->update($validated);
        
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
} 