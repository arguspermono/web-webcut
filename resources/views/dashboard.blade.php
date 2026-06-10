@extends('layouts.app')

@section('title', 'WebCut Lite - Dashboard')

@section('header')
    @include('partials.navbar')
@endsection

@section('content')
<div class="flex-1 p-8 lg:p-16 max-w-6xl w-full mx-auto space-y-16">

    {{-- STATS GRID --}}
    <section>
        <div class="stats stats-vertical sm:stats-horizontal w-full shadow">

            {{-- Stat 1: Total Projects --}}
            <div class="stat bg-base-100">
                <div class="stat-figure text-base-content/30">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="9" y1="21" x2="9" y2="9"/>
                    </svg>
                </div>
                <div class="stat-title font-bold uppercase tracking-wider text-xs">Total Projects</div>
                <div class="stat-value">{{ $totalVideos }}</div>
                <div class="stat-desc">All uploaded videos</div>
            </div>

            {{-- Stat 2: Storage Used --}}
            <div class="stat bg-neutral text-neutral-content">
                <div class="stat-figure text-neutral-content/30">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <ellipse cx="12" cy="5" rx="9" ry="3"/>
                        <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                        <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                    </svg>
                </div>
                <div class="stat-title font-bold uppercase tracking-wider text-xs text-neutral-content/60">Storage Used</div>
                <div class="stat-value">{{ number_format($totalStorage / 1048576, 2) }} <span class="text-2xl font-bold">MB</span></div>
                <div class="stat-desc text-neutral-content/50">Local disk usage</div>
            </div>

            {{-- Stat 3: Total Duration --}}
            <div class="stat bg-base-100">
                <div class="stat-figure text-base-content/30">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div class="stat-title font-bold uppercase tracking-wider text-xs">Total Duration</div>
                <div class="stat-value font-mono text-3xl">{{ gmdate("H:i:s", $totalDuration ?? 0) }}</div>
                <div class="stat-desc">Combined video length</div>
            </div>

        </div>
    </section>

    {{-- PROJECTS SECTION --}}
    <section class="space-y-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h2 class="text-2xl font-bold tracking-tight">Recent Projects</h2>
                <div class="badge badge-soft badge-neutral badge-sm">Latest</div>
            </div>
            <a href="{{ route('workspace') }}" class="btn btn-ghost btn-sm font-bold">View all &rarr;</a>
        </div>

        @if($projects->isEmpty())
            {{-- EMPTY STATE --}}
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body items-center text-center py-24 gap-6">
                    <div class="p-6 bg-base-200 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.5" class="text-base-content/30">
                            <rect x="1" y="5" width="15" height="14" rx="2"/>
                            <polygon points="23 7 16 12 23 17 23 7"/>
                        </svg>
                    </div>
                    <div class="space-y-2">
                        <p class="text-xl font-bold">No projects yet</p>
                        <p class="text-base-content/50">Upload your first video to get started.</p>
                    </div>
                    <button
                        class="btn btn-neutral font-bold px-8 mt-2"
                        onclick="document.getElementById('upload-modal').showModal()"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        New Project
                    </button>
                </div>
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
