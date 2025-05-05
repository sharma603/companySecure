<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    public function index()
    {
        try {
            $notes = Note::with(['company', 'user'])->latest()->get();
            $companies = Company::all();
            
            // Add debug logging
            Log::info('Notes index loaded', [
                'notes_count' => $notes->count(),
                'companies_count' => $companies->count(),
                'companies' => $companies->pluck('name', 'id')
            ]);
            
            // If no companies, check if we need to create some test data
            if ($companies->isEmpty()) {
                Log::warning('No companies found when loading notes index, creating test data');
                
                // Create a test company if none exist (emergency fallback)
                $testCompany = Company::create([
                    'name' => 'Test Company',
                    'contact' => '123-456-7890',
                    'address' => 'Test Address',
                    'url' => 'https://example.com'
                ]);
                
                Log::info('Created test company', ['company_id' => $testCompany->id]);
                
                // Refresh the companies collection
                $companies = Company::all();
            }
            
            return view('notes.index', compact('notes', 'companies'));
        } catch (\Exception $e) {
            Log::error('Error fetching notes: ' . $e->getMessage());
            return back()->with('error', 'Failed to fetch notes.');
        }
    }

    public function show($id)
    {
        try {
            $note = Note::with(['company', 'user'])->findOrFail($id);
            return response()->json(['success' => true, 'note' => $note]);
        } catch (\Exception $e) {
            Log::error('Error fetching note: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Note not found'], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('Note creation request:', [
                'data' => $request->all(),
                'referrer' => $request->headers->get('referer')
            ]);

            // Validate the request
            $validated = $request->validate([
                'company_id' => 'required|exists:companies,id',
                'data' => 'required|array',
                'data.*.type' => 'required|string|in:custom,email,gmail,outlook,github,windows,number,text',
                'data.*.value' => 'required|string',
                'data.*.label' => 'required|string',
                'data.*.secondValue' => 'nullable|string'
            ], [
                'company_id.required' => 'Please select a company',
                'company_id.exists' => 'The selected company is invalid',
                'data.required' => 'At least one note field is required',
                'data.array' => 'Note data must be structured properly',
                'data.*.type.required' => 'Field type is required for all entries',
                'data.*.type.in' => 'Invalid field type selected',
                'data.*.value.required' => 'Value is required for all fields',
                'data.*.value.string' => 'Field values must be text',
                'data.*.label.required' => 'Label is required for all fields',
                'data.*.label.string' => 'Field labels must be text'
            ]);

            if (!Auth::check()) {
                Log::warning('Unauthorized note creation attempt');
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to create notes'
                ], 401);
            }

            $userId = Auth::id();
            Log::info('Authenticated user creating note:', [
                'user_id' => $userId,
                'auth_guard' => Auth::getDefaultDriver(),
                'user_type' => get_class(Auth::user())
            ]);

            // Create note with the data
            $note = Note::create([
                'company_id' => $request->input('company_id'),
                'user_id' => $userId,
                'data' => $request->input('data')
            ]);

            Log::info('Note created successfully:', [
                'note_id' => $note->id,
                'company_id' => $note->company_id,
                'user_id' => $note->user_id
            ]);

            // Check if the request is coming from a company dashboard
            $referrer = $request->headers->get('referer');
            $isFromCompanyDashboard = false;
            
            if ($referrer) {
                $referrerPath = parse_url($referrer, PHP_URL_PATH);
                $isFromCompanyDashboard = strpos($referrerPath, "/companies/{$note->company_id}/dashboard") !== false;
            }
            
            // If the request is AJAX, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Note created successfully',
                    'note' => $note->load(['company', 'user']),
                    'redirect' => $isFromCompanyDashboard ? route('companies.dashboard', $note->company_id) : null
                ]);
            }
            
            // For regular form submission, redirect appropriately
            if ($isFromCompanyDashboard) {
                return redirect()->route('companies.dashboard', $note->company_id)
                    ->with('success', 'Note created successfully');
            }

            return redirect()->route('notes.index')
                ->with('success', 'Note created successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Note creation validation failed:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating note:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create note: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info('Note update request:', [
                'id' => $id,
                'data' => $request->all()
            ]);

            // More specific validation rules
            $validated = $request->validate([
                'company_id' => 'required|integer|exists:companies,id',
                'data' => 'required|array',
                'data.*.type' => 'required|string|in:custom,email,gmail,outlook,github,windows,number,text',
                'data.*.value' => 'required|string',
                'data.*.label' => 'required|string',
                'data.*.secondValue' => 'nullable|string'
            ], [
                'company_id.required' => 'Please select a company',
                'company_id.exists' => 'The selected company is invalid',
                'data.required' => 'At least one note field is required',
                'data.array' => 'Note data must be structured properly',
                'data.*.type.required' => 'Field type is required for all entries',
                'data.*.type.in' => 'Invalid field type selected',
                'data.*.value.required' => 'Value is required for all fields',
                'data.*.value.string' => 'Field values must be text',
                'data.*.label.required' => 'Label is required for all fields',
                'data.*.label.string' => 'Field labels must be text'
            ]);

            $note = Note::findOrFail($id);
            
            Log::info('Found note for update:', [
                'note_id' => $note->id,
                'current_company_id' => $note->company_id,
                'new_company_id' => $request->input('company_id'),
                'current_data' => $note->data,
                'new_data' => $request->input('data')
            ]);
            
            // Update the note
            $note->update([
                'company_id' => $request->input('company_id'),
                'data' => $request->input('data')
            ]);
            
            Log::info('Note updated successfully:', [
                'note_id' => $note->id,
                'updated_data' => $note->fresh()->data
            ]);
            
            return response()->json(['success' => true, 'note' => $note->fresh()]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Note update validation failed:', [
                'id' => $id,
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating note:', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update note: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $note = Note::findOrFail($id);
            $note->delete();

            return redirect()->back()->with('success', 'Note deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting note: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete note.');
        }
    }
} 