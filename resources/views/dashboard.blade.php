@extends('layouts.app')

@section('title', 'WebCut Lite - Dashboard')

@section('header')
    @include('partials.navbar')
@endsection

@section('content')
<div class="flex-1 p-8 lg:p-16 max-w-6xl w-full mx-auto space-y-16">

    {{-- STATS GRID --}}
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        
        {{-- Stat 1: White Card --}}
        <div class="bg-white rounded-selector ] border border-gray-200 p-8 flex flex-col justify-between gap-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="bg-lime-200 w-fit px-3 py-1 rounded-selector                 <span class="text-xs font-bold uppercase tracking-wider text-lime-900">Total Projects</span>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gray-50 rounded-selector flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-400"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                </div>
                <span class="text-5xl font-extrabold tracking-tight text-black">{{ $totalVideos }}</span>
            </div>
        </div>

        {{-- Stat 2: Black Card --}}
        <div class="bg-[#111111] rounded-selector ] p-8 flex flex-col justify-between gap-6 shadow-lg text-white">
            <div class="bg-white/10 w-fit px-3 py-1 rounded-selector                 <span class="text-xs font-bold uppercase tracking-wider text-white">Storage Used</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-5xl font-extrabold tracking-tight">{{ number_format($totalStorage / 1048576, 2) }}</span>
                <span class="text-xl font-bold text-gray-400">MB</span>
            </div>
        </div>

        {{-- Stat 3: White Card --}}
        <div class="bg-white rounded-selector ] border border-gray-200 p-8 flex flex-col justify-between gap-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="bg-lime-200 w-fit px-3 py-1 rounded-selector                 <span class="text-xs font-bold uppercase tracking-wider text-lime-900">Total Duration</span>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gray-50 rounded-selector flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-gray-400"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <span class="text-4xl font-extrabold tracking-tight text-black font-mono">{{ gmdate("H:i:s", $totalDuration ?? 0) }}</span>
            </div>
        </div>

    </section>

    {{-- PROJECTS SECTION --}}
    <section class="space-y-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h2 class="text-2xl font-bold tracking-tight text-black">Recent Projects</h2>
                <div class="bg-lime-300 w-12 h-2 rounded-selector hidden sm:block"></div>
            </div>
            <a href="{{ route('workspace') }}" class="btn btn-sm btn-ghost text-gray-500 hover:text-black hover:bg-gray-100 rounded-selector px-4 font-bold">View all &rarr;</a>
        </div>

        @if($projects->isEmpty())
            {{-- EMPTY STATE --}}
            <div class="bg-white rounded-selector ] border border-gray-200 py-24
                        flex flex-col items-center justify-center gap-6 shadow-sm">
                <div class="p-6 bg-gray-50 rounded-selector ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.5" class="text-gray-400">
                        <rect x="1" y="5" width="15" height="14" rx="2"/>
                        <polygon points="23 7 16 12 23 17 23 7"/>
                    </svg>
                </div>
                <div class="text-center space-y-2">
                    <p class="text-xl font-bold text-black">No projects yet</p>
                    <p class="text-gray-500">Upload your first video to get started.</p>
                </div>
                <button
                    class="btn bg-[#111111] text-white hover:bg-black border-none rounded-selector font-bold px-8 mt-2"
                    onclick="document.getElementById('upload-modal').showModal()"
                >
                    + New Project
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($projects as $project)
                    @include('partials.project-card', ['project' => $project])
                @endforeach
            </div>
        @endif
    </section>

</div>
@endsection

@push('modals')
    @include('partials.upload-modal')
@endpush
