{{--
    Dashboard / main site navbar.
--}}
<div class="navbar bg-base-100 px-6 py-4 h-24 z-20 sticky top-0 border-b border-gray-200 w-full">

    {{-- Start: Drawer Toggle + Logo --}}
    <div class="navbar-start flex items-center gap-2">
        <label for="main-drawer" aria-label="open sidebar" class="btn btn-square btn-ghost text-base-content/60 hover:text-base-content">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor" class="w-6 h-6">
                <path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path>
                <path d="M9 4v16"></path><path d="M14 10l2 2l-2 2"></path>
            </svg>
        </label>
        
        <a href="{{ route('dashboard') }}" class="btn btn-ghost gap-3 px-3 ml-2 hidden sm:flex">
            <div class="w-8 h-8 bg-lime-300 rounded-xl flex items-center justify-center shrink-0 text-black">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="23 7 16 12 23 17 23 7"/>
                    <rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
                </svg>
            </div>
            <span class="text-xl font-bold tracking-tight text-base-content">WebCut</span>
        </a>
    </div>

    {{-- Center: (empty) --}}
    <div class="navbar-center hidden lg:flex flex-1"></div>

    <div class="navbar-end flex-none justify-end">
        <button
            class="btn btn-neutral rounded-full font-bold px-8 shadow-sm h-12 gap-2"
            onclick="document.getElementById('upload-modal').showModal()"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Project
        </button>
    </div>

</div>