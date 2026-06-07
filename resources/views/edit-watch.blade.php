<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch Edited Video - WebCut</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen flex flex-col">
    <nav class="bg-gray-800 p-4 shadow-md flex justify-between items-center">
        <div class="text-xl font-bold tracking-wider">
            WebCut <span class="text-purple-500">Edit Output</span>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="text-gray-300 hover:text-white transition mr-4">Dashboard</a>
            <a href="{{ asset('storage/' . $mediaEdit->output_path) }}" download class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded shadow-md transition text-sm">Download Video</a>
        </div>
    </nav>

    <main class="flex-1 container mx-auto px-4 py-8 max-w-4xl flex flex-col items-center">
        <h1 class="text-3xl font-light mb-6 text-center text-gray-200">Your Final Render</h1>

        <div class="w-full bg-black rounded-lg shadow-2xl overflow-hidden mb-6 aspect-video">
            <video 
                controls 
                class="w-full h-full object-contain"
                preload="metadata"
                controlsList="nodownload"
            >
                <source src="{{ route('media.edit.stream', $mediaEdit) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 w-full shadow-inner">
            <h2 class="text-xl font-semibold mb-4 text-purple-400">Edit Details</h2>
            <ul class="space-y-2 text-gray-300">
                <li><strong class="text-gray-400">Original File:</strong> {{ $mediaEdit->media->original_filename }}</li>
                <li><strong class="text-gray-400">Created At:</strong> {{ $mediaEdit->created_at->format('M d, Y H:i:s') }}</li>
                @if(!empty($mediaEdit->edit_params))
                    <li><strong class="text-gray-400">Trim Range:</strong> 
                        {{ number_format($mediaEdit->edit_params['start_time'] ?? 0, 2) }}s - 
                        {{ number_format($mediaEdit->edit_params['end_time'] ?? 0, 2) }}s
                    </li>
                @endif
            </ul>
        </div>
    </main>

    <footer class="bg-gray-950 p-4 text-center text-gray-500 text-sm">
        &copy; {{ date('Y') }} WebCut. All rights reserved.
    </footer>
</body>
</html>
