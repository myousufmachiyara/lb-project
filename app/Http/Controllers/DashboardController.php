<?php

namespace App\Http\Controllers;

use App\Models\{Project, ProjectStatus};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log, Storage};
use Exception;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Total number of projects with status_id = 2
            $totalProjects = Project::where('status_id', 2)->count();
    
            // Total number of pieces in projects with status_id = 2
            $totalPiecesInProcess = Project::totalPiecesInProcess();

    
            return view('home', compact('totalProjects', 'totalPiecesInProcess'));
        } catch (Exception $e) {
            Log::error('Dashboard index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load dashboard data.');
        }
    }
}
