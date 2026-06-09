@extends('layouts.app')

@section('title', 'Workspace - WebCut Lite')

@section('header')
    @include('partials.navbar')
@endsection

@section('content')
<div class="p-8 lg:p-12 max-w-7xl w-full mx-auto space-y-10">

    {{-- HEADER & SEARCH --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight">Workspace</h1>
            <p class="text-base-content/60 font-medium mt-2 text-lg">Manage all your raw and edited projects.</p>
        </div>
        
        <form action="{{ route('workspace') }}" method="GET" class="flex items-center gap-3 w-full md:w-auto">
            <input type="hidden" name="filter" value="{{ request('filter') }}">
            <label class="input input-bordered flex items-center gap-2 w-full md:w-72 rounded-full h-12">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-5 h-5 opacity-50"><path fill-rule="evenodd" d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z" clip-rule="evenodd" /></svg>
                <input type="text" name="search" value="{{ request('search') }}" class="grow" placeholder="Search projects..." />
            </label>
            <button type="submit" class="btn btn-neutral rounded-full px-6 font-bold h-12">Search</button>
        </form>
    </div>

    {{-- FILTER TABS --}}
    <div role="tablist" class="tabs tabs-border overflow-x-auto">
        <a href="{{ route('workspace', ['search' => request('search')]) }}" 
           role="tab" class="tab font-bold text-sm sm:text-base {{ !request('filter') ? 'tab-active' : '' }}">
            All Projects
        </a>
        <a href="{{ route('workspace', ['filter' => 'raw', 'search' => request('search')]) }}" 
           role="tab" class="tab font-bold text-sm sm:text-base {{ request('filter') === 'raw' ? 'tab-active' : '' }}">
            Raw Uploads
        </a>
        <a href="{{ route('workspace', ['filter' => 'edited', 'search' => request('search')]) }}" 
           role="tab" class="tab font-bold text-sm sm:text-base {{ request('filter') === 'edited' ? 'tab-active' : '' }}">
            Edited Outputs
        </a>
    </div>

    {{-- PROJECT GRID --}}
    @if($projects->isEmpty())
        {{-- EMPTY STATE --}}
        <div class="card bg-base-100 border border-base-200 shadow-sm mt-8">
            <div class="card-body items-center text-center py-24 gap-6">
                <div class="p-6 bg-base-200 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-base-content/30">
                        <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
                <div class="text-center space-y-2">
                    <p class="text-xl font-bold">No projects found</p>
                    <p class="text-base-content/50">Try adjusting your search query or category filter.</p>
                </div>
                @if(request('search') || request('filter'))
                    <a href="{{ route('workspace') }}" class="btn btn-ghost mt-2 font-bold">Clear Filters</a>
                @endif
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-8">
            @foreach($projects as $project)
                @include('partials.project-card', ['project' => $project])
            @endforeach
        </div>
        
        {{-- PAGINATION --}}
        @if($projects->hasPages())
            <div class="mt-12 flex justify-center">
                <div class="bg-base-100 rounded-box p-2 border border-base-200 shadow-sm inline-flex">
                    {{ $projects->links('pagination::tailwind') }}
                </div>
            </div>
        @endif
    @endif

</div>
@endsection

@push('modals')
    @include('partials.upload-modal')
@endpush
