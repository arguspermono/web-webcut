# WebCut Roadmap

## Vision
Build a scalable, browser-based multimedia streaming and video editing platform using Laravel 13 and FFMPEG with a strict plan-driven, agent-orchestrated architecture.

## Goals
1. **Robust Architecture:** Establish a scalable, modular foundation based on clear agent responsibilities.
2. **Seamless Video Upload & Processing:** Enable users to upload media which is automatically transcoded and prepared for streaming via FFMPEG.
3. **In-Browser Video Editing:** Provide users with essential video editing tools (trim, crop, mute, speed, text overlay) entirely within the browser.
4. **Optimized Progressive Streaming:** Deliver smooth video playback using HTTP Range Requests.
5. **Secure & Production-Ready:** Implement strict validation and sanitization, particularly around shell commands and file uploads.

## Timeline
1. **Planning & Architecture Setup (Current):** Define milestones, dependencies, and all system architecture documents.
2. **Foundational Scaffolding:** Set up Laravel folder structure, core routing, and database schema.
3. **Core Services Implementation:** Build out upload processing, FFMPEG queues, and streaming controllers.
4. **Frontend Integration:** Develop the Video.js player, editing interface (noUiSlider, etc.), and AJAX communication.
5. **Polishing & Hardening:** Refine FFMPEG commands, optimize streaming, and lock down security.
