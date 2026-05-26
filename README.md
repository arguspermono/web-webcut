# WebCut

WebCut is a premium, monolithic Laravel-based multimedia streaming and browser-based video editing platform. It provides a sleek, dark-themed user interface to upload video files, watch them via progressive streaming, select segment ranges (using a custom timeline slider), and perform complex FFmpeg operations like trimming, speed adjustments, and audio muting directly from the web browser.

---

## Key Features

1. **Secure Ingestion & Auto-Transcoding**: Validates media uploads and normalizes video in the background into web-optimized H.264/AAC MP4 formats (`-movflags +faststart`) while generating thumbnail previews.
2. **HTTP 206 Partial Content Streaming**: Handles HTTP range requests for instant seek/scrubbing capability.
3. **Glassmorphic Interactive Editor**: A responsive, premium dark-mode dashboard powered by Video.js, noUiSlider, and custom CSS variables.
4. **Complex Edit Pipeline**: Combines trimming, speed adjustments (utilizing chained `atempo` filters for arbitrary speed factors), and audio modifications into a single-pass, highly optimized FFmpeg pipeline processed asynchronously via background queues.
5. **Robust Test Coverage**: Includes feature integration tests for the upload/edit lifecycle and unit tests for internal audio speed calculation structures.

---

## System Requirements

- **PHP**: `^8.2` (Laravel 13 compatible)
- **Composer**
- **Node.js & npm**
- **SQLite**
- **FFmpeg**: Must be installed globally and accessible in your system's `PATH`.

---

## Installation & Setup

Follow these steps to set up the project locally:

1. **Clone the repository** and navigate to the project directory:
   ```bash
   git clone https://github.com/arguspermono/web-webcut.git
   cd web-webcut
   ```

2. **Install PHP and JS dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment File**:
   Copy the example environment file and generate the application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Prepare Database**:
   WebCut uses MySQL. Create the `webcut` database in MySQL and run the migrations:
   ```bash
   # Log into MySQL and create the database (if not using a GUI like phpMyAdmin/HeidiSQL)
   mysql -u root -e "CREATE DATABASE IF NOT EXISTS webcut;"
   
   # Run migrations to populate the tables
   php artisan migrate
   ```

5. **Link Public Storage**:
   Create a symbolic link from `public/storage` to `storage/app/public` so files are accessible to the browser:
   ```bash
   php artisan storage:link
   ```

---

## Running the Application

To run WebCut, you need to start three processes simultaneously:

1. **Vite Development Server** (handles asset building and hot reload):
   ```bash
   npm run dev
   ```

2. **Laravel Server** (handles HTTP requests):
   ```bash
   php artisan serve
   ```

3. **Queue Worker** (processes transcodes and edit jobs in the background):
   ```bash
   php artisan queue:work
   ```

Now, navigate to [http://localhost:8000](http://localhost:8000) in your web browser.

---

## Running Tests

Run the test suite using Artisan:

```bash
php artisan test
```

*Note: Ensure your CLI environment has the `pdo_sqlite` PHP extension enabled to execute the test suite successfully.*
