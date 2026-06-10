<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Only fetch 3 for the dashboard
        $projects = Media::orderBy('created_at', 'desc')->take(3)->get();
        
        // Totals should represent all data, not just the latest 3
        $totalVideos = Media::count();
        $totalStorage = Media::sum('size_bytes');
        $totalDuration = Media::sum('duration');

        return view('dashboard', compact('projects', 'totalVideos', 'totalStorage', 'totalDuration'));
    }

    public function workspace(Request $request)
    {
        $query = Media::query();

        // Search by filename
        if ($request->filled('search')) {
            $query->where('original_filename', 'like', '%' . $request->search . '%');
        }

        // Filter by edited/raw 
        if ($request->filled('filter')) {
            if ($request->filter === 'raw') {
                $query->doesntHave('edits');
            } elseif ($request->filter === 'edited') {
                $query->has('edits');
            }
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        return view('workspace', compact('projects'));
    }
}
