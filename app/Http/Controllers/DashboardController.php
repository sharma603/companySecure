<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Reminder;
use App\Models\Company;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Note;
use App\Models\User;
use App\Models\Register;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login to access the dashboard.');
            }

            // Get company statistics
            $totalCompanies = Company::count();
            $recentCompanies = Company::latest()->take(5)->get();
            
            // Get notes statistics
            $totalNotes = Note::count();
            $recentNotes = Note::with('company', 'user')->latest()->take(5)->get();
            
            // Get upcoming reminders (due within the next 7 days or overdue)
            $upcomingReminders = Reminder::where(function($query) use ($user) {
                    // If admin, show all reminders, otherwise filter by user
                    if ($user instanceof Register && method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                        // Show all reminders for admins
                    } else {
                        $query->where('user_id', $user->id);
                    }
                })
                ->where(function($query) {
                    $query->where('reminder_date', '>=', Carbon::now())
                          ->where('reminder_date', '<=', Carbon::now()->addDays(7))
                          ->orWhere(function($q) {
                              $q->where('reminder_date', '<', Carbon::now())
                                ->where('is_completed', false);
                          });
                })
                ->with('company')
                ->orderBy('reminder_date', 'asc')
                ->take(5)
                ->get();
            
            // Get reminder statistics
            $totalReminders = Reminder::count();
            $overdueReminders = Reminder::where('reminder_date', '<', Carbon::now())
                ->where('is_completed', false)
                ->count();
            $completedReminders = Reminder::where('is_completed', true)->count();
            
            // Get role and permission statistics
            $totalRoles = Role::count();
            $totalPermissions = Permission::count();
            $roles = Role::withCount('registers')->orderBy('registers_count', 'desc')->take(5)->get();
            
            // Get user statistics
            $totalUsers = Register::count();
            $recentUsers = Register::latest()->take(5)->get();
            
            // Calculate login statistics (mocked for demonstration)
            $loginStats = [
                'today' => rand(5, 20),
                'week' => rand(20, 50),
                'month' => rand(50, 200)
            ];
            
            // Get activity statistics for charts
            $remindersByMonth = DB::table('reminders')
                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->get()
                ->pluck('count', 'month')
                ->toArray();
            
            $notesByMonth = DB::table('notes')
                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->get()
                ->pluck('count', 'month')
                ->toArray();
            
            return view('layouts.dashboard', [
                'user' => $user,
                'title' => 'Dashboard',
                'totalCompanies' => $totalCompanies,
                'recentCompanies' => $recentCompanies,
                'totalNotes' => $totalNotes,
                'recentNotes' => $recentNotes,
                'upcomingReminders' => $upcomingReminders,
                'totalReminders' => $totalReminders,
                'overdueReminders' => $overdueReminders,
                'completedReminders' => $completedReminders,
                'totalRoles' => $totalRoles,
                'totalPermissions' => $totalPermissions,
                'roles' => $roles,
                'totalUsers' => $totalUsers,
                'recentUsers' => $recentUsers,
                'loginStats' => $loginStats,
                'remindersByMonth' => $remindersByMonth,
                'notesByMonth' => $notesByMonth
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')->with('error', 'An error occurred while loading the dashboard.');
        }
    }
} 