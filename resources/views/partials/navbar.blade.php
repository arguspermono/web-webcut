{{--
    Dashboard / main site navbar.
--}}
<div class="navbar bg-[#FCFCFC] px-6 py-4 h-24 z-20 sticky top-0 border-b border-gray-200 w-full">

    {{-- Start: Drawer Toggle + Logo --}}
    <div class="navbar-start flex items-center gap-2">
        <label for="main-drawer" aria-label="open sidebar" class="btn btn-square btn-ghost text-gray-500 hover:text-black">
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
            <span class="text-xl font-bold tracking-tight text-black">WebCut</span>
        </a>
    </div>

    {{-- Center: (empty) --}}
    <div class="navbar-center hidden lg:flex flex-1"></div>

    {{-- End: Primary action --}}
    <div class="navbar-end flex-none justify-end">
        <button
            class="btn bg-[#111111] text-white hover:bg-black border-none rounded-full font-bold px-8 shadow-sm"
            onclick="document.getElementById('upload-modal').showModal()"
        >
            + New Project
        </button>
    </div>

</div>