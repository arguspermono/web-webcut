import './bootstrap';
import axios from 'axios';
import videojs from 'video.js';
import noUiSlider from 'nouislider';

document.addEventListener('DOMContentLoaded', () => {

    const uploadZone   = document.getElementById('upload-zone');
    const fileInput    = document.getElementById('file-input');
    const editorZone   = document.getElementById('editor-zone');
    const uploadLoader = document.getElementById('upload-loader');
    const btnTrim      = document.getElementById('btn-trim');

    let player        = null;
    let slider        = null;
    let sliderValues  = [0, 0];
    let currentMediaId = null;

    const csrfToken = () =>
        document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ─── Upload ──────────────────────────────────────────────────────────────
    uploadZone.addEventListener('click', () => fileInput.click());

    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('drag-active');
    });
    uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('drag-active'));
    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('drag-active');
        if (e.dataTransfer.files.length) handleUpload(e.dataTransfer.files[0]);
    });
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length) handleUpload(e.target.files[0]);
    });

    function handleUpload(file) {
        if (!file.type.startsWith('video/')) {
            alert('Please select a valid video file.');
            return;
        }

        const formData = new FormData();
        formData.append('video', file);
        formData.append('_token', csrfToken());

        uploadLoader.style.display = 'block';
        uploadZone.style.pointerEvents = 'none';

        axios.post('/media/upload', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        }).then(response => {
            currentMediaId = response.data.media_id;
            uploadLoader.innerText = 'Processing video…';
            pollMediaStatus(currentMediaId);
        }).catch(err => {
            console.error(err);
            alert('Upload failed. Please try again.');
            uploadLoader.style.display = 'none';
            uploadZone.style.pointerEvents = 'auto';
        });
    }

    // Poll media status until 'ready'
    function pollMediaStatus(mediaId, attempts = 0) {
        if (attempts > 30) {
            alert('Processing timed out. Please try again.');
            return;
        }
        setTimeout(() => {
            // For simplicity, just wait and load. A GET /media/{id}/status route can be added in Phase 8.
            // For now, assume processing completes within ~4 seconds for small files.
            if (attempts >= 4) {
                loadEditor(mediaId);
            } else {
                pollMediaStatus(mediaId, attempts + 1);
            }
        }, 1000);
    }

    // ─── Editor ───────────────────────────────────────────────────────────────
    function loadEditor(mediaId) {
        uploadZone.style.display = 'none';
        editorZone.style.display = 'block';

        player = videojs('video-player', {
            controls: true,
            autoplay: false,
            preload: 'auto',
            sources: [{ src: `/stream/${mediaId}`, type: 'video/mp4' }],
        });

        player.on('loadedmetadata', () => {
            sliderValues = [0, player.duration()];
            initSlider(player.duration());
        });
    }

    function formatTime(seconds) {
        const s   = Number(seconds);
        const m   = Math.floor(s / 60);
        const sec = Math.floor(s % 60);
        const ms  = Math.floor((s - Math.floor(s)) * 100);
        return `${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}.${String(ms).padStart(2,'0')}`;
    }

    function initSlider(duration) {
        const sliderEl = document.getElementById('timeline-slider');
        if (slider) slider.destroy();

        slider = noUiSlider.create(sliderEl, {
            start:   [0, duration],
            connect: true,
            range:   { min: 0, max: duration },
        });

        slider.on('update', (values, handle) => {
            sliderValues[handle] = parseFloat(values[handle]);
            document.getElementById('time-start').innerText = formatTime(sliderValues[0]);
            document.getElementById('time-end').innerText   = formatTime(sliderValues[1]);
            if (player && !player.paused()) player.pause();
            if (player) player.currentTime(sliderValues[handle]);
        });
    }

    // ─── Trim Submission ─────────────────────────────────────────────────────
    btnTrim.addEventListener('click', () => {
        if (!currentMediaId) return;

        btnTrim.disabled    = true;
        btnTrim.innerText   = 'Processing…';

        const payload = new URLSearchParams({
            _token:     csrfToken(),
            media_id:   currentMediaId,
            start_time: sliderValues[0],
            end_time:   sliderValues[1],
        });

        axios.post('/media/edit', payload).then(response => {
            const editId = response.data.media_edit_id;
            pollEditStatus(editId);
        }).catch(err => {
            console.error(err);
            alert('Edit request failed.');
            btnTrim.disabled  = false;
            btnTrim.innerText = 'Trim Video';
        });
    });

    function pollEditStatus(editId, attempts = 0) {
        if (attempts > 60) {
            alert('Edit processing timed out.');
            btnTrim.disabled  = false;
            btnTrim.innerText = 'Trim Video';
            return;
        }

        setTimeout(() => {
            axios.get(`/media/edit/${editId}`).then(response => {
                const { status, download_url } = response.data;

                if (status === 'ready' && download_url) {
                    btnTrim.disabled  = false;
                    btnTrim.innerText = 'Trim Video';
                    offerDownload(download_url);
                } else if (status === 'failed') {
                    alert('Edit processing failed.');
                    btnTrim.disabled  = false;
                    btnTrim.innerText = 'Trim Video';
                } else {
                    pollEditStatus(editId, attempts + 1);
                }
            }).catch(() => pollEditStatus(editId, attempts + 1));
        }, 2000);
    }

    function offerDownload(url) {
        const a = document.createElement('a');
        a.href     = url;
        a.download = 'webcut-edited.mp4';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

});
