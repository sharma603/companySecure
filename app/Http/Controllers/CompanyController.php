<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index()
    {
        try {
            $companies = Company::all();
            return response()->json([
                'status' => 'success',
                'data' => $companies
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch companies: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch companies'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'contact' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'url' => 'nullable|url|max:255'
            ]);

            if (Auth::check()) {
                $validated['user_id'] = Auth::id();
            }

            $company = Company::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Company created successfully',
                'data' => $company
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create company: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create company: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Company $company)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => $company
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch company: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch company'
            ], 500);
        }
    }

    public function update(Request $request, Company $company)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'contact' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'url' => 'nullable|url|max:255'
            ]);

            $company->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Company updated successfully',
                'data' => $company
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update company: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update company'
            ], 500);
        }
    }

    public function destroy(Company $company)
    {
        try {
            $company->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Company deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete company: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete company'
            ], 500);
        }
    }

    public function dashboard(Company $company)
    {
        $notes = $company->notes()->with('user')->orderBy('created_at', 'desc')->get();
        return view('companydashboard', compact('company', 'notes'));
    }

    // ...other methods...
}