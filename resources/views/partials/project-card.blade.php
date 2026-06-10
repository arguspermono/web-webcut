{{--
    Single project card.
    Required variables:
      $project — Eloquent Media model
--}}
<div class="bg-white rounded-selector border-2 border-transparent shadow-md hover:shadow-lg overflow-hidden transition-all group flex flex-col relative">

    {{-- Thumbnail --}}
    <div class="aspect-video bg-gray-100 relative overflow-hidden m-2 rounded-selector">
        @if($project->thumbnail_path)
            <img src="{{ asset('storage/' . $project->thumbnail_path) }}"
                 alt="Thumbnail"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <div class="w-full h-full flex items-center justify-center bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.5" class="text-gray-300">
                    <rect x="1" y="5" width="15" height="14" rx="2"/>
                    <polygon points="23 7 16 12 23 17 23 7"/>
                </svg>
            </div>
        @endif

        {{-- Status badge — maps every DB enum value to its own style --}}
        @php
            $displayStatus = $project->status;
            if ($project->status === 'ready') {
                $hasEdits = $project->edits()->where('status', 'ready')->exists();
                $displayStatus = $hasEdits ? 'edited' : 'raw';
            }

            $statusMap = [
                'uploading'  => ['label' => 'Uploading',  'class' => 'bg-blue-100 text-blue-700 border border-blue-200'],
                'processing' => ['label' => 'Processing', 'class' => 'bg-amber-100 text-amber-700 border border-amber-200'],
                'raw'        => ['label' => 'Raw',        'class' => 'bg-gray-100 text-gray-600 border border-gray-200'],
                'edited'     => ['label' => 'Edited',     'class' => 'bg-lime-300 text-black border border-lime-400'],
                'failed'     => ['label' => 'Failed',     'class' => 'bg-red-100 text-red-600 border border-red-200'],
            ];
            $badge = $statusMap[$displayStatus] ?? ['label' => ucfirst($displayStatus), 'class' => 'bg-gray-100 text-gray-500 border border-gray-200'];
        @endphp
        <div class="absolute top-3 left-3">
            <span class="badge {{ $badge['class'] }} font-bold uppercase tracking-wider text-[10px] px-3 py-3 rounded-selector shadow-sm">
                {{ $badge['label'] }}
            </span>
        </div>
    </div>

    {{-- Info --}}
    <div class="p-6 flex flex-col gap-4 flex-1">
        <div class="flex flex-col gap-1">
            <p class="text-lg font-bold text-black truncate" title="{{ $project->original_filename }}">
                {{ $project->original_filename }}
            </p>
            <p class="text-xs text-gray-500 font-medium tracking-wide">{{ $project->created_at->timezone('Asia/Jakarta')->format('M d, Y · H:i') }} WIB</p>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2 mt-auto flex-wrap">
            <a href="{{ route('project.edit', $project->id) }}"
               class="btn btn-sm btn-ghost border border-gray-200 hover:border-black
                      hover:bg-transparent text-black rounded-selector font-bold px-4 transition-all">
                Edit
            </a>

            @if($project->status === 'ready')
                <a href="{{ route('project.watch', $project->id) }}"
                   class="btn btn-sm bg-[#111111] text-white hover:bg-black
                          border-none rounded-selector font-bold px-4">
                    Watch
                </a>
            @endif

            <div class="dropdown dropdown-top dropdown-end ml-auto">
                <div tabindex="0" role="button" class="btn btn-ghost btn-sm btn-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                </div>
                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-48 border border-base-200">
                    <li>
                        <a onclick="document.getElementById('rename-modal-{{ $project->id }}').showModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Rename
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('project.destroy', $project->id) }}" method="POST" class="w-full p-0 m-0" onsubmit="return confirm('Delete this project?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full flex items-center gap-2 text-error text-left py-2 px-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                Delete
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Rename Modal --}}
    <dialog id="rename-modal-{{ $project->id }}" class="modal text-left">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Rename Project</h3>
            <p class="text-xs text-gray-500 mb-4">Created: {{ $project->created_at->timezone('Asia/Jakarta')->format('M d, Y') }}</p>
            <form action="{{ route('project.rename', $project->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-semibold">Project Name</span>
                    </label>
                    @php
                        $extension = pathinfo($project->original_filename, PATHINFO_EXTENSION);
                        $basename = pathinfo($project->original_filename, PATHINFO_FILENAME);
                    @endphp
                    <input type="hidden" name="original_filename_ext" value="{{ $extension }}">
                    <label class="input input-bordered flex items-center gap-2">
                        <input type="text" name="original_filename_base" value="{{ $basename }}" class="grow" required />
                        @if($extension)
                            <span class="badge badge-ghost font-mono">.{{ $extension }}</span>
                        @endif
                    </label>
                </div>
                <div class="modal-action">
                    <button type="button" class="btn" onclick="document.getElementById('rename-modal-{{ $project->id }}').close()">Cancel</button>
                    <button type="submit" class="btn btn-neutral">Save</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</div>
