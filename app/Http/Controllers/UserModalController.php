<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Register;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class UserModalController extends Controller
{
    /**
     * Create a new user from modal form
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'model_type' => 'nullable|string'
        ]);

        try {
            // Determine which model to use (User or Register)
            $modelType = $validated['model_type'] ?? config('auth.providers.users.model', 'App\\Models\\User');
            $modelClass = $modelType === 'App\\Models\\Register' ? Register::class : User::class;
            
            // Check if email already exists in the selected model
            $emailExists = $modelClass::where('email', $validated['email'])->exists();
            if ($emailExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email already exists',
                    'errors' => ['email' => ['The email has already been taken.']]
                ], 422);
            }
            
            // Create the user
            $user = $modelClass::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            
            // Assign roles
            if (isset($validated['roles']) && method_exists($user, 'roles')) {
                $user->roles()->sync($validated['roles']);
            }

            Log::info('User created successfully via modal', [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'model_type' => $modelType,
                'roles' => $validated['roles'] ?? []
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('User creation failed via modal: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $validated
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }
} 