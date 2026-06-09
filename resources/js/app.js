import axios from 'axios';
import videojs from 'video.js';

document.addEventListener('DOMContentLoaded', () => {

    const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // ─── Dashboard Upload Logic ──────────────────────────────────────────────
    const uploadZone = document.getElementById('upload-zone');
    const fileInput = document.getElementById('file-input');
    const uploadLoader = document.getElementById('upload-loader');

    if (uploadZone) {
        uploadZone.addEventListener('click', (e) => {
            if (e.target !== fileInput && !e.target.closest('label')) {
                fileInput.click();
            }
        });
        uploadZone.addEventListener('dragover', (e) => { e.preventDefault(); uploadZone.classList.add('drag-active'); });
        uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('drag-active'));
        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('drag-active');
            if (e.dataTransfer.files.length) handleUpload(e.dataTransfer.files[0]);
        });
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length) handleUpload(e.target.files[0]);
        });
    }

    function handleUpload(file) {
        if (!file.type.startsWith('video/')) { alert('Please select a valid video file.'); return; }
        
        const formData = new FormData();
        formData.append('video', file);
        formData.append('_token', csrfToken());

        uploadLoader.style.display = 'block';
        uploadZone.style.pointerEvents = 'none';

        axios.post('/project/upload', formData, { headers: { 'Content-Type': 'multipart/form-data' } })
            .then(response => {
                const mediaId = response.data.media_id;
                uploadLoader.innerText = 'Processing video…';
                pollMediaStatus(mediaId);
            }).catch(err => {
                console.error(err);
                alert('Upload failed.');
                uploadLoader.style.display = 'none';
                uploadZone.style.pointerEvents = 'auto';
            });
    }

    function pollMediaStatus(mediaId, attempts = 0) {
        if (attempts > 60) { alert('Processing timed out.'); return; }
        setTimeout(() => {
            axios.get(`/project/${mediaId}/status`)
                .then(response => {
                    const { status } = response.data;
                    if (status === 'ready') window.location.href = `/project/${mediaId}/edit`;
                    else if (status === 'failed') alert('Processing failed.');
                    else { pollMediaStatus(mediaId, attempts + 1); }
                }).catch(() => pollMediaStatus(mediaId, attempts + 1));
        }, 2000);
    }


    // ─── Visual Timeline Editor Logic ────────────────────────────────────────
    const editorTimeline = document.getElementById('visual-timeline');
    if (editorTimeline && window.WebCutConfig) {
        const config = window.WebCutConfig;
        
        // Initialize player
        const player = document.getElementById('video-player');
        
        // Editor controls
        const btnPlayPause = document.getElementById('btn-play-pause');
        const iconPlayPause = document.getElementById('icon-play-pause');
        const ctrlSpeed = document.getElementById('ctrl-speed');
        const ctrlMute = document.getElementById('ctrl-mute');


        // Timeline elements
        const trimLeft = document.getElementById('trim-left');
        const trimRight = document.getElementById('trim-right');
        const selectedArea = document.getElementById('timeline-selected');
        const playhead = document.getElementById('playhead');
        const timeStartLabel = document.getElementById('time-start');
        const timeEndLabel = document.getElementById('time-end');
        const thumbnailsContainer = document.getElementById('timeline-thumbnails');
        
        let duration = config.duration;
        let startPercent = 0;
        let endPercent = 100;
        let isDragging = null; // 'left', 'right', 'playhead', null
        let isSeeking = false; // Guard: prevent boundary loop during active seeks
        const draftKey = 'webcut_draft_' + config.mediaId;

        // isSeeking is managed inside seekAndPlay — no global listener needed

        const getVidDur = () => (player.duration && !isNaN(player.duration)) ? player.duration : duration;

        let seekId = 0;
        const seekTo = (time) => {
            return new Promise((resolve) => {
                const currentSeekId = ++seekId;
                isSeeking = true;

                const onSeeked = () => {
                    // Mencegah memory leak dan race condition jika ada seek baru yang menimpa
                    if (currentSeekId !== seekId) {
                        player.removeEventListener('seeked', onSeeked);
                        return;
                    }
                    player.removeEventListener('seeked', onSeeked);
                    isSeeking = false;
                    resolve();
                };

                player.addEventListener('seeked', onSeeked);

                try {
                    player.currentTime = time;
                } catch (e) {
                    console.error('Seek error:', e);
                    player.removeEventListener('seeked', onSeeked);
                    isSeeking = false;
                    resolve();
                }
            });
        };

        const togglePlay = async () => {
            const vidDur = getVidDur();
            const startTime = (startPercent / 100) * vidDur;
            const endTime = (endPercent / 100) * vidDur;

            if (player.paused) {
                const tolerance = 0.15;
                // Jika posisi playhead sekarang kurang dari startTime ATAU sudah di akhir endTime
                if (player.currentTime < (startTime - tolerance) || player.currentTime >= (endTime - tolerance)) {
                    await seekTo(startTime);
                }

                const p = player.play();
                if (p !== undefined) {
                    p.catch(console.error);
                }
                iconPlayPause.innerHTML = '<rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect>';
            } else {
                player.pause();
                iconPlayPause.innerHTML = '<polygon points="5 3 19 12 5 21 5 3"></polygon>';
            }
        };

        btnPlayPause.addEventListener('click', (e) => {
            console.log('PLAY CLICK');
            console.log('startPercent =', startPercent);
            console.log('endPercent =', endPercent);
            e.stopPropagation(); 
            togglePlay(); 
        });
        const videoContainer = document.getElementById('video-container');
        if (videoContainer) videoContainer.addEventListener('click', togglePlay);

        ctrlSpeed.addEventListener('change', (e) => { player.playbackRate = parseFloat(e.target.value); });
        ctrlMute.addEventListener('change',  (e) => { player.muted = e.target.checked; });
        const loadThumbnails = () => {
            const numThumbs = Math.ceil(duration);
            let targetCount = 6;
            
            if (duration < 60) {
                targetCount = 5; // 4 to 6
            } else if (duration <= 300) {
                targetCount = 8; // 6 to 10
            } else {
                targetCount = 12; // Max 10 to 12
            }
            
            const step = Math.max(1, Math.ceil(numThumbs / targetCount));
            
            for (let i = 1; i <= numThumbs; i += step) {
                const thumb = document.createElement('div');
                const frameStr = String(i).padStart(4, '0');
                const url = `/storage/media/thumbnails/${config.mediaId}/${frameStr}.jpg`;
                
                // Bulletproof method to NEVER stretch: bg-cover on a div
                thumb.className = "h-full flex-1 min-w-0 bg-cover bg-center bg-no-repeat pointer-events-none opacity-50 grayscale-[20%]";
                thumb.style.backgroundImage = `url('${url}')`;
                
                thumbnailsContainer.appendChild(thumb);
            }
        };

        if (duration > 0 && config.status === 'ready') {
            loadThumbnails();
            // Safely snap to initial start after metadata loads
            if (player.readyState >= 1) {
                player.currentTime = (startPercent / 100) * duration;
            } else {
                player.addEventListener('loadedmetadata', () => {
                    duration = player.duration;
                    updateUI();
                });
            }
        }

        const formatTime = (seconds) => {
            const m = Math.floor(seconds / 60);
            const s = Math.floor(seconds % 60);
            const ms = Math.floor((seconds - Math.floor(seconds)) * 100);
            return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}.${String(ms).padStart(2,'0')}`;
        };

        const updateUI = () => {
            trimLeft.style.left = `${startPercent}%`;
            trimRight.style.left = `${endPercent}%`;
            selectedArea.style.left = `${startPercent}%`;
            selectedArea.style.width = `${endPercent - startPercent}%`;
            
            timeStartLabel.innerText = formatTime((startPercent / 100) * duration);
            timeEndLabel.innerText = formatTime((endPercent / 100) * duration);
        };

        // Dragging logic
        const getPercent = (clientX) => {
            const rect = editorTimeline.getBoundingClientRect();
            let x = clientX - rect.left;
            let percent = (x / rect.width) * 100;
            return Math.max(0, Math.min(100, percent));
        };

        trimLeft.addEventListener('mousedown', (e) => { isDragging = 'left'; e.preventDefault(); });
        trimRight.addEventListener('mousedown', (e) => { isDragging = 'right'; e.preventDefault(); });
        editorTimeline.addEventListener('mousedown', (e) => {
            if(e.target === trimLeft || e.target === trimRight) return;
            isDragging = 'playhead';
            const p = getPercent(e.clientX);
            // Strictly bind playhead clicks to within the selected range
            let boundedP = Math.max(startPercent, Math.min(endPercent, p));
            player.currentTime = (boundedP / 100) * duration;
        });

        window.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            const p = getPercent(e.clientX);
            
            if (isDragging === 'left') {
                startPercent = Math.min(p, endPercent - 1); // 1% minimum gap
                updateUI();
                seekTo((startPercent / 100) * duration);
            } else if (isDragging === 'right') {
                endPercent = Math.max(p, startPercent + 1);
                updateUI();
                seekTo((endPercent / 100) * duration);
            } else if (isDragging === 'playhead') {
                // Strictly bind playhead dragging to within the selected range
                let boundedP = Math.max(startPercent, Math.min(endPercent, p));
                seekTo((boundedP / 100) * duration);
            }
        });

        window.addEventListener('mouseup', () => { 
            console.log('MOUSE UP');
            console.log('FINAL startPercent =', startPercent);
            console.log('FINAL endPercent =', endPercent);
            isDragging = null; 
        });

        // Playhead sync
        player.addEventListener('timeupdate', () => {
            // Skip while browser is seeking — prevents infinite seek loop
            if (isSeeking) return;

            const vidDur = getVidDur();
            const currentTime = player.currentTime;
            const startTime = (startPercent / 100) * vidDur;
            const endTime = (endPercent / 100) * vidDur;

            // Stop di trim end
            if (!player.paused && currentTime >= endTime) {
                player.pause();
                try { player.currentTime = endTime; } catch(e){}
                iconPlayPause.innerHTML = '<polygon points="5 3 19 12 5 21 5 3"></polygon>';
                return;
            }

            // Update visual playhead — skip while dragging handles
            if (isDragging === 'left' || isDragging === 'right') return;
            const rawPercent    = (currentTime / vidDur) * 100;
            const clampedPercent = Math.max(startPercent, Math.min(endPercent, rawPercent));
            playhead.style.left = `${clampedPercent}%`;
        });

        updateUI();

        // Reset Button
        const btnReset = document.getElementById('btn-reset');
        if (btnReset) {
            btnReset.addEventListener('click', () => {
                startPercent = 0;
                endPercent = 100;
                ctrlSpeed.value = "1";
                ctrlMute.checked = false;
                player.playbackRate = 1.0;
                player.muted = false;
                localStorage.removeItem(draftKey);
                updateUI();
                player.currentTime = 0;
            });
        }

        // Save Draft (Local)
        const btnSaveDraft = document.getElementById('btn-save-draft');
        if (btnSaveDraft) {
            btnSaveDraft.addEventListener('click', () => {
                const draft = {
                    startPercent,
                    endPercent,
                    speed: ctrlSpeed.value,
                    mute: ctrlMute.checked
                };
                localStorage.setItem(draftKey, JSON.stringify(draft));
                
                // Visual feedback
                const originalText = btnSaveDraft.innerText;
                btnSaveDraft.innerText = 'Saved!';
                btnSaveDraft.classList.add('text-lime-600');
                setTimeout(() => {
                    btnSaveDraft.innerText = originalText;
                    btnSaveDraft.classList.remove('text-lime-600');
                }, 2000);
            });
        }

        // Load Draft on Init
        const savedDraft = localStorage.getItem(draftKey);
        if (savedDraft) {
            try {
                const draft = JSON.parse(savedDraft);
                startPercent = draft.startPercent || 0;
                endPercent = draft.endPercent || 100;
                if (draft.speed) ctrlSpeed.value = draft.speed;
                if (draft.mute) ctrlMute.checked = true;
                
                // Wait for player to be ready to apply settings
                player.addEventListener('loadeddata', () => {
                    player.playbackRate = parseFloat(ctrlSpeed.value);
                    player.muted = ctrlMute.checked;
                    updateUI();
                }, { once: true });
                
                // If already loaded
                if (player.readyState >= 2) {
                    player.playbackRate = parseFloat(ctrlSpeed.value);
                    player.muted = ctrlMute.checked;
                    updateUI();
                }
            } catch (e) {
                console.error('Failed to parse draft', e);
            }
        }

        // Save Project
        document.getElementById('btn-save-project').addEventListener('click', (e) => {
            const btn = e.target;
            btn.disabled = true;
            btn.innerText = 'Processing...';

            const payload = new URLSearchParams({
                _token: csrfToken(),
                media_id: config.mediaId,
                start_time: (startPercent / 100) * duration,
                end_time: (endPercent / 100) * duration,
                speed: ctrlSpeed.value,
                mute: ctrlMute.checked ? 1 : 0
            });

            axios.post('/project/edit', payload).then(response => {
                const editId = response.data.media_edit_id;
                pollEditStatus(editId, btn);
            }).catch(err => {
                console.error(err);
                alert('Save request failed.');
                btn.disabled = false;
                btn.innerText = 'Save & Process';
            });
        });

        function pollEditStatus(editId, btn, attempts = 0) {
            if (attempts > 60) { alert('Processing timed out.'); btn.disabled = false; btn.innerText = 'Save & Process'; return; }
            
            // Granular UI feedback
            if (attempts > 0 && attempts < 5) {
                btn.innerText = 'Processing...';
            } else if (attempts >= 5) {
                btn.innerText = 'Rendering...';
            }

            setTimeout(() => {
                axios.get(`/project/edit/${editId}/status`).then(response => {
                    const { status, download_url } = response.data;
                    if (status === 'ready' && download_url) {
                        btn.innerText = 'Done!';
                        setTimeout(() => window.location.href = `/project/${config.mediaId}/watch`, 1000);
                    } else if (status === 'failed') {
                        alert('Processing failed.');
                        btn.disabled = false;
                        btn.innerText = 'Save & Process';
                    } else {
                        pollEditStatus(editId, btn, attempts + 1);
                    }
                }).catch(() => pollEditStatus(editId, btn, attempts + 1));
            }, 2000);
        }
    }

    // ─── Watch Page Logic ────────────────────────────────────────────────────
    if (document.querySelector('.watch-body')) {
        videojs('video-player');
    }
});