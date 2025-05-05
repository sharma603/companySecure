<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NewUserCredentials;

class SubUserController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Only require authentication, not admin role
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new sub-user.
     */
    public function create()
    {
        // For debugging, log that we entered this method
        Log::info('SubUserController@create method called');
        
        try {
            // Get all roles
            $roles = Role::all();
            Log::info('Roles retrieved: ' . $roles->count());
            
            // Return the view
            return view('roles.create_sub_user', compact('roles'));
        } catch (\Exception $e) {
            Log::error('Error in SubUserController@create: ' . $e->getMessage());
            
            // Return a simple view for testing
            return view('roles.create_sub_user');
        }
    }

    /**
     * Store a newly created sub-user in storage.
     */
    public function store(Request $request)
    {
        Log::info('SubUserController@store method called');
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'send_credentials' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            
            Log::info('User created with ID: ' . $user->id);

            // Assign the selected role or default to 'user' role
            $roleId = $request->role_id;
            
            if (!$roleId) {
                // Get default user role
                $role = Role::where('name', 'user')->first();
                if ($role) {
                    $roleId = $role->id;
                    Log::info('Using default user role with ID: ' . $roleId);
                }
            }
            
            if ($roleId) {
                $user->roles()->attach($roleId);
                Log::info('Role attached to user');
            }

            // Send credentials via email if requested
            if ($request->send_credentials) {
                try {
                    Mail::to($user->email)->send(new NewUserCredentials($user, $request->password));
                    Log::info('Credentials email sent to: ' . $user->email);
                    session()->flash('mail_success', 'Login credentials have been emailed to the user.');
                } catch (\Exception $e) {
                    Log::error('Error sending email: ' . $e->getMessage());
                    session()->flash('mail_error', 'Could not send email with credentials: ' . $e->getMessage());
                }
            }

            Log::info('Redirecting to roles.index after successful creation');
            return redirect()->route('roles.index')
                ->with('success', 'Sub-user created successfully. They can now log in with their credentials.');
                
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }
} 