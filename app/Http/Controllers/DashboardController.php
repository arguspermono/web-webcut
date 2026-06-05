<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $projects = Media::orderBy('created_at', 'desc')->get();
        $totalVideos = $projects->count();
        $totalStorage = $projects->sum('size_bytes');
        $totalDuration = $projects->sum('duration');

        return view('dashboard', compact('projects', 'totalVideos', 'totalStorage', 'totalDuration'));
    }
}
