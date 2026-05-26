# Frontend UI Architecture

## Philosophy
The UI follows a sleek, dark-themed, glassmorphic design that screams "Premium Video Editor". It features vibrant gradients, modern typography, and smooth micro-interactions.

## DOM Structure
1. **Container (`.app-container`)**: Full viewport height and width. Centered content.
2. **Header (`.app-header`)**: Simple, elegant branding ("WebCut").
3. **Workspace (`.workspace`)**:
   - **Upload Zone (`#upload-zone`)**: A drag-and-drop area with dashed borders, hover states, and a hidden `<input type="file">`.
   - **Editor Zone (`#editor-zone`)**: Hidden by default. Contains:
     - **Player (`.player-wrapper`)**: Contains the `video.js` element.
     - **Timeline (`.timeline-wrapper`)**: Contains the `noUiSlider` element representing the video duration.
     - **Controls (`.editor-controls`)**: Start/End time readouts and the final "Trim Video" CTA button.

## CSS Implementation (Vanilla CSS)
- **Variables**: HSL colors for background (`--bg-dark`), primary accent (`--accent-purple`, `--accent-blue`), and surface colors.
- **Glassmorphism**: `backdrop-filter: blur(16px)` on floating panels.
- **Responsive**: Flexbox layout that centers the workspace and adapts to smaller screens.

## JS Flow
1. User drops a video -> Axios posts to `/media/upload` with FormData.
2. Status is polled or assumed "processing". For MVP, assume it takes a few seconds or poll if necessary.
3. Once ready, `video.js` is initialized with `/stream/{uuid}`.
4. `video.js` metadata loaded -> `noUiSlider` is configured with `[0, video.duration()]`.
5. User adjusts slider -> Video seeks to the new start/end to preview.
