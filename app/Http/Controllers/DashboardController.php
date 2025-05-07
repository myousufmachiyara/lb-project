<?php

namespace App\Http\Controllers;

use App\Models\{Project, ProjectStatus, Task};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log, Storage};
use Exception;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Total number of projects with status_id = 2
            $totalProjects = Project::where('status_id', 2)->count();
    
            // Total number of pieces in projects with status_id = 2
            $totalPiecesInProcess = Project::totalPiecesInProcess();
    
            // Tasks due within the next 2 days
            $today = Carbon::today();
            $afterTwoDays = Carbon::today()->addDays(2);
            $upcomingTasks = Task::whereBetween('due_date', [$today, $afterTwoDays])
                                 ->with(['project', 'status', 'category']) // eager load relationships
                                 ->get();
    
            return view('home', compact('totalProjects', 'totalPiecesInProcess', 'upcomingTasks'));
        } catch (Exception $e) {
            Log::error('Dashboard index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load dashboard data.');
        }
    }    
}
