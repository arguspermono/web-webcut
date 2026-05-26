# WebCut System Diagrams

These diagrams visualize the architecture, data models, user flows, and use cases for the WebCut multimedia platform.

## 1. Entity-Relationship Diagram (ERD)

```mermaid
erDiagram
    USERS {
        uuid id PK
        string name
        string email
        string password
        string role "user, admin"
        timestamp created_at
        timestamp updated_at
    }
    
    MEDIA {
        uuid id PK
        uuid user_id FK
        string original_filename
        string storage_path
        string thumbnail_path
        string status "uploading, processing, ready, failed"
        float duration
        string resolution
        string mime_type
        bigint size_bytes
        timestamp created_at
        timestamp updated_at
    }
    
    MEDIA_EDITS {
        uuid id PK
        uuid parent_media_id FK
        string storage_path
        json edit_parameters
        string status "processing, ready, failed"
        timestamp created_at
        timestamp updated_at
    }
    
    USERS ||--o{ MEDIA : "uploads"
    MEDIA ||--o{ MEDIA_EDITS : "has derivatives"
```

## 2. Activity Diagram (Upload & Processing)

```mermaid
stateDiagram-v2
    [*] --> Uploading : User selects video
    Uploading --> Validating : Dropzone AJAX POST
    Validating --> FailedValidation : Invalid MIME/Size
    FailedValidation --> [*]
    
    Validating --> SavedToStorage : Validation Passed
    SavedToStorage --> Processing : Dispatch InitialTranscodeJob
    Processing --> FFMPEG_Transcoding : Generate MP4 (H.264)
    FFMPEG_Transcoding --> FFMPEG_Thumbnail : Extract Thumbnail
    FFMPEG_Thumbnail --> Ready : Update DB Status
    Ready --> [*] : Video available for streaming
```

## 3. Architecture Diagram

```mermaid
flowchart TB
    subgraph Client [Client / Browser]
        UI[Blade Templates + Bootstrap]
        Player[Video.js Player]
        Editor[noUiSlider Editor UI]
    end

    subgraph Server [Web Server - Apache / Laravel 13]
        Router[API & Web Routes]
        Controllers[Thin Controllers]
        Services[MediaService, FFmpegService, StreamingService]
        Jobs[Laravel Queue Jobs]
    end

    subgraph Infrastructure [Infrastructure]
        DB[(MySQL Database)]
        Queue[(Redis / DB Queue)]
        Storage[Local File Storage]
        FFMPEG{FFMPEG / FFProbe}
    end

    UI -->|AJAX POST Upload| Router
    Editor -->|AJAX POST Edits| Router
    Player -->|HTTP Range Requests| Router

    Router --> Controllers
    Controllers --> Services
    Services --> DB
    Services --> Queue
    Services --> Storage

    Queue -->|Pops Jobs| Jobs
    Jobs --> FFMPEG
    FFMPEG -->|Reads/Writes| Storage
    Jobs -->|Updates Status| DB
```

## 4. Use Case Diagram

```mermaid
flowchart LR
    User([User])
    Admin([Admin])
    
    subgraph WebCut Platform
        UC1(Upload Video)
        UC2(Edit Video)
        UC3(Trim/Crop/Speed/Mute)
        UC4(Preview Video)
        UC5(View Content Feed)
        UC6(Stream Video)
        UC7(Manage All Media)
        UC8(Manage Users)
    end
    
    User --> UC1
    User --> UC2
    User --> UC5
    User --> UC6
    
    Admin --> UC1
    Admin --> UC2
    Admin --> UC5
    Admin --> UC6
    Admin --> UC7
    Admin --> UC8
    
    UC2 -.-> |Includes| UC3
    UC2 -.-> |Includes| UC4
    UC6 -.-> |Progressive Download| UC4
```
