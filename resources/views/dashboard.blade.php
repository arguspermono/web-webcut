<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebCut Lite - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="dashboard-body">
    <header class="app-header">
        <h1>WebCut Workspace</h1>
        <button class="btn-primary" onclick="document.getElementById('upload-modal').style.display='flex'">+ New Project</button>
    </header>

    <main class="dashboard-main">
        <section class="stats-grid">
            <div class="stat-card">
                <h3>Total Projects</h3>
                <p>{{ $totalVideos }}</p>
            </div>
            <div class="stat-card">
                <h3>Total Storage Used</h3>
                <p>{{ number_format($totalStorage / 1048576, 2) }} MB</p>
            </div>
            <div class="stat-card">
                <h3>Total Duration</h3>
                <p>{{ gmdate("H:i:s", $totalDuration ?? 0) }}</p>
            </div>
        </section>

        <section class="projects-section">
            <h2>Recent Projects</h2>
            <div class="projects-grid">
                @forelse($projects as $project)
                <div class="project-card">
                    <div class="project-thumbnail">
                        @if($project->thumbnail_path)
                            <img src="{{ asset('storage/' . $project->thumbnail_path) }}" alt="Thumbnail">
                        @else
                            <div class="placeholder-thumb">{{ ucfirst($project->status) }}</div>
                        @endif
                    </div>
                    <div class="project-info">
                        <h4>{{ $project->original_filename }}</h4>
                        <p>Status: {{ $project->status }}</p>
                        <div class="project-actions">
                            <a href="{{ route('project.edit', $project->id) }}" class="btn-secondary">Edit</a>
                            @if($project->status === 'ready')
                            <a href="{{ route('project.watch', $project->id) }}" class="btn-secondary">Watch</a>
                            @endif
                            <form action="{{ route('project.destroy', $project->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger" onclick="return confirm('Delete this project?')">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <p>No projects found. Create one to get started!</p>
                @endforelse
            </div>
        </section>
    </main>

    <!-- Upload Modal -->
    <div id="upload-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <span class="close-btn" onclick="document.getElementById('upload-modal').style.display='none'">&times;</span>
            <h2>Create New Project</h2>
            <div class="upload-zone" id="upload-zone">
                <p>Drag and drop your video here or click to browse</p>
                <div class="loader" id="upload-loader" style="display:none;">Uploading...</div>
                <input type="file" id="file-input" accept="video/mp4,video/webm,video/quicktime">
            </div>
        </div>
    </div>
</body>
</html>
