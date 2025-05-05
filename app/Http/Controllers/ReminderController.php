<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    public function index()
    {
        $reminders = Reminder::with(['company', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('reminder_date', 'asc')
            ->get();
            
        // Get user companies or empty collection if there are none
        $companies = $this->getUserCompanies();
        
        return view('reminders.index', compact('reminders', 'companies'));
    }

    public function create()
    {
        // Get user companies or empty collection if there are none
        $companies = $this->getUserCompanies();
        return view('reminders.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reminder_date' => 'required|date',
            'reminder_email' => 'nullable|email',
            'company_id' => 'nullable|exists:companies,id'
        ]);

        $user = Auth::user();
        
        // If company_id is an empty string, convert it to null
        if (isset($validated['company_id']) && $validated['company_id'] === '') {
            $validated['company_id'] = null;
        }
        
        // Company check only if a company is selected
        if (!empty($validated['company_id'])) {
            $company = Company::findOrFail($validated['company_id']);
            
            // Check if the user has access to this company
            $userCompanyIds = $this->getUserCompanies()->pluck('id')->toArray();
            if (!in_array($company->id, $userCompanyIds)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You do not have access to this company'
                    ], 403);
                }
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You do not have access to this company');
            }
        }

        $validated['user_id'] = $user->id;
        $validated['is_completed'] = $request->has('is_completed');
        $validated['email_sent'] = false;

        $reminder = Reminder::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reminder created successfully',
                'reminder' => $reminder->load(['company', 'user'])
            ]);
        }

        return redirect()->route('reminders.index')
            ->with('success', 'Reminder created successfully.');
    }

    public function edit(Reminder $reminder)
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'reminder' => $reminder
            ]);
        }
        
        // Get user companies or empty collection if there are none
        $companies = $this->getUserCompanies();
        return view('reminders.edit', compact('reminder', 'companies'));
    }

    public function update(Request $request, Reminder $reminder)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reminder_date' => 'required|date',
            'reminder_email' => 'nullable|email',
            'company_id' => 'nullable|exists:companies,id'
        ]);

        // If company_id is an empty string, convert it to null
        if (isset($validated['company_id']) && $validated['company_id'] === '') {
            $validated['company_id'] = null;
        }

        $validated['is_completed'] = $request->has('is_completed');
        
        // If the date has changed, reset the email_sent flag
        if ($reminder->reminder_date->format('Y-m-d') !== date('Y-m-d', strtotime($validated['reminder_date']))) {
            $validated['email_sent'] = false;
        }

        $reminder->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reminder updated successfully',
                'reminder' => $reminder->fresh()->load(['company', 'user'])
            ]);
        }

        return redirect()->route('reminders.index')
            ->with('success', 'Reminder updated successfully.');
    }

    public function destroy(Reminder $reminder)
    {
        $reminder->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reminder deleted successfully'
            ]);
        }

        return redirect()->route('reminders.index')
            ->with('success', 'Reminder deleted successfully.');
    }
    
    /**
     * Get the companies associated with the current user
     * 
     * @return \Illuminate\Support\Collection
     */
    private function getUserCompanies()
    {
        $user = Auth::user();
        
        if (!$user) {
            return collect([]);
        }
        
        // Check if user model has a companies relationship method
        if (method_exists($user, 'companies')) {
            return $user->companies;
        }
        
        // Fallback - search for companies related to this user in reminders
        $companyIds = Reminder::where('user_id', $user->id)
            ->whereNotNull('company_id')
            ->distinct()
            ->pluck('company_id');
            
        return Company::whereIn('id', $companyIds)->get();
    }
} 