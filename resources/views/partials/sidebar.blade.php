{{--
    Sidebar Navigation (Drawer Side)
--}}
<div class="drawer-side z-40 is-drawer-close:overflow-visible">
    <label for="main-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
    
    <aside class="flex min-h-full flex-col items-start bg-[#FCFCFC] border-r border-gray-200 
                  is-drawer-close:w-20 is-drawer-open:w-64 w-64 lg:w-auto transition-all duration-300">
        
        {{-- Navigation Links --}}
        <ul class="menu w-full px-4 py-8 space-y-2 grow">
            
            {{-- Dashboard Link --}}
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="is-drawer-close:tooltip is-drawer-close:tooltip-right flex items-center gap-4 px-4 py-3 rounded-2xl font-bold transition-all 
                          {{ request()->routeIs('dashboard') ? 'bg-[#111111] text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100 hover:text-black' }}"
                   data-tip="Dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                    <span class="is-drawer-close:hidden whitespace-nowrap text-base">Dashboard</span>
                </a>
            </li>

            {{-- Workspace Link --}}
            <li>
                <a href="{{ route('workspace') }}" 
                   class="is-drawer-close:tooltip is-drawer-close:tooltip-right flex items-center gap-4 px-4 py-3 rounded-2xl font-bold transition-all 
                          {{ request()->routeIs('workspace') ? 'bg-[#111111] text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100 hover:text-black' }}"
                   data-tip="Workspace">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    <span class="is-drawer-close:hidden whitespace-nowrap text-base">Workspace</span>
                </a>
            </li>

        </ul>
    </aside>
</div>
