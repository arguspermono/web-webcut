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
            <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight text-black">Workspace</h1>
            <p class="text-gray-500 font-medium mt-2 text-lg">Manage all your raw and edited projects.</p>
        </div>
        
        <form action="{{ route('workspace') }}" method="GET" class="flex items-center gap-3 w-full md:w-auto">
            <input type="hidden" name="filter" value="{{ request('filter') }}">
            <div class="relative w-full md:w-72">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search projects..." 
                       class="input bg-white border-2 border-gray-200 focus:border-black focus:ring-0 w-full pl-12 rounded-full font-bold placeholder-gray-400 shadow-sm h-12">
            </div>
            <button type="submit" class="btn bg-[#111111] hover:bg-black text-white border-none rounded-full px-6 font-bold shadow-sm h-12">Search</button>
        </form>
    </div>

    {{-- FILTER TABS --}}
    <div class="flex items-center gap-2 border-b border-gray-200 pb-px overflow-x-auto no-scrollbar">
        <a href="{{ route('workspace', ['search' => request('search')]) }}" 
           class="px-6 py-3 font-bold text-sm sm:text-base border-b-4 transition-colors whitespace-nowrap
                  {{ !request('filter') ? 'border-lime-300 text-black' : 'border-transparent text-gray-400 hover:text-black hover:border-gray-200' }}">
            All Projects
        </a>
        <a href="{{ route('workspace', ['filter' => 'raw', 'search' => request('search')]) }}" 
           class="px-6 py-3 font-bold text-sm sm:text-base border-b-4 transition-colors whitespace-nowrap
                  {{ request('filter') === 'raw' ? 'border-lime-300 text-black' : 'border-transparent text-gray-400 hover:text-black hover:border-gray-200' }}">
            Raw Uploads
        </a>
        <a href="{{ route('workspace', ['filter' => 'edited', 'search' => request('search')]) }}" 
           class="px-6 py-3 font-bold text-sm sm:text-base border-b-4 transition-colors whitespace-nowrap
                  {{ request('filter') === 'edited' ? 'border-lime-300 text-black' : 'border-transparent text-gray-400 hover:text-black hover:border-gray-200' }}">
            Edited Outputs
        </a>
    </div>

    {{-- PROJECT GRID --}}
    @if($projects->isEmpty())
        {{-- EMPTY STATE --}}
        <div class="bg-white rounded-[2rem] border border-gray-200 py-24 flex flex-col items-center justify-center gap-6 shadow-sm">
            <div class="p-6 bg-gray-50 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-gray-400">
                    <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <div class="text-center space-y-2">
                <p class="text-xl font-bold text-black">No projects found</p>
                <p class="text-gray-500">Try adjusting your search query or category filter.</p>
            </div>
            @if(request('search') || request('filter'))
                <a href="{{ route('workspace') }}" class="btn btn-ghost hover:bg-gray-100 rounded-full font-bold px-6 mt-2 text-gray-500 hover:text-black">Clear Filters</a>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($projects as $project)
                @include('partials.project-card', ['project' => $project])
            @endforeach
        </div>
        
        {{-- PAGINATION --}}
        @if($projects->hasPages())
            <div class="mt-12 flex justify-center">
                <div class="bg-white rounded-full p-2 border border-gray-200 shadow-sm inline-flex">
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
