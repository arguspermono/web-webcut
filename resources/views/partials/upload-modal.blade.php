{{--
    Upload modal (DaisyUI <dialog>).
    Rendered via @stack('modals') in layouts/app.blade.php.
    JavaScript hooks: #upload-modal, #upload-zone, #file-input, #upload-loader
--}}
<dialog id="upload-modal" class="modal">
    <div class="modal-box bg-white border border-gray-100 rounded-[2.5rem] max-w-lg p-10 shadow-2xl relative text-black">
        {{-- Close button --}}
        <button
            class="btn btn-sm btn-circle btn-ghost absolute right-6 top-6 text-gray-400 hover:bg-gray-100"
            onclick="document.getElementById('upload-modal').close()"
        >✕</button>

        <h2 class="text-3xl font-extrabold tracking-tight mb-2">New Project</h2>
        <p class="text-base text-gray-500 font-medium mb-8">Upload a video to start editing.</p>

        {{-- Drop zone --}}
        <div class="upload-zone border-2 border-dashed border-gray-200 rounded-[2rem] p-12 bg-gray-50
                    flex flex-col items-center justify-center gap-6 cursor-pointer
                    hover:border-lime-400 hover:bg-lime-50 transition-all group"
             id="upload-zone">

            <div class="w-16 h-16 bg-lime-200 rounded-full flex items-center justify-center text-lime-900 group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
            </div>

            <div class="text-center">
                <p class="font-bold text-lg text-black">Drag &amp; drop your video here</p>
                <p class="text-sm text-gray-500 font-medium mt-1">MP4, WebM, MOV supported</p>
            </div>

            <label class="btn bg-white border-2 border-gray-200 rounded-full font-bold px-8
                          hover:bg-gray-50 hover:border-black text-black transition-all cursor-pointer shadow-sm">
                Browse Files
                <input type="file" id="file-input"
                       accept="video/mp4,video/webm,video/quicktime" class="hidden">
            </label>

            <div class="loader hidden text-lime-600 text-sm font-bold bg-lime-100 px-4 py-2 rounded-full" id="upload-loader">
                Uploading…
            </div>
        </div>
    </div>

    {{-- Backdrop closes modal --}}
    <form method="dialog" class="modal-backdrop bg-black/40">
        <button></button>
    </form>
</dialog>
