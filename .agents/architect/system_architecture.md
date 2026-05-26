# WebCut System Architecture

## Overview
WebCut is a browser-based multimedia platform built on a monolithic Laravel 13 backend, communicating with a modern frontend via AJAX and progressive streaming protocols.

## High-Level Components

### 1. Client (Browser)
- **UI:** Vanilla JS + Bootstrap, Blade Templates
- **Video Player:** Video.js for HTML5 video playback
- **Editor Controls:** noUiSlider for timeline and parameter adjustments
- **Communication:** AJAX requests to backend APIs, HTTP Range requests for video streaming

### 2. Web Server / App Server
- **Server:** Apache2
- **Framework:** Laravel 13 (PHP 8.4+)
- **Responsibilities:** Routing, authentication (if any), file upload handling, job dispatching, streaming data delivery, and API endpoints for editor interactions.

### 3. Asynchronous Processing (Queue Workers)
- **Tech:** Laravel Queue (Database or Redis driven), Supervisor for process management.
- **Responsibilities:** Executing long-running FFMPEG commands for transcoding, thumbnail extraction, and complex video edits without blocking the web server.

### 4. Database
- **Tech:** MySQL
- **Responsibilities:** Storing media metadata (filename, duration, status, paths), user data, and job status.

### 5. File Storage
- **Tech:** Local File System (storage/app/public/media)
- **Responsibilities:** Storing original uploads, transcoded files, thumbnails, and final edited versions.
