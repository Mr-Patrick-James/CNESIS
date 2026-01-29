# Directory Setup Script

This document outlines the directory structure needed for file uploads in the CNESIS system.

## Required Directories

### 1. Program Images Directory
- **Path**: `assets/img/programs/`
- **Purpose**: Store uploaded program images (JPG, PNG, WebP)
- **Max File Size**: 5MB per file
- **Naming Convention**: `{programCode}-{timestamp}.{extension}`

### 2. Prospectus Files Directory  
- **Path**: `assets/prospectus/`
- **Purpose**: Store uploaded prospectus files (PDF, Excel, Word)
- **Max File Size**: 10MB per file
- **Naming Convention**: `{programCode}-prospectus-{timestamp}.{extension}`

## Manual Setup Instructions

Create these directories manually using your file manager or command line:

```bash
# Create directories
mkdir -p assets/img/programs
mkdir -p assets/prospectus

# Set permissions (Linux/Mac)
chmod 755 assets/img/programs
chmod 755 assets/prospectus

# For WAMP on Windows, ensure web server has write permissions
```

## Directory Structure After Setup

```
CNESIS/
├── assets/
│   ├── img/
│   │   └── programs/          # Program images go here
│   └── prospectus/            # Prospectus files go here
├── api/
│   └── programs/
│       ├── upload-image.php
│       └── upload-prospectus.php
└── views/
    └── admin/
        └── features/
            └── programs.php   # Upload interface
```

## File Upload Features

### Supported File Types

**Images:**
- JPG/JPEG
- PNG  
- WebP

**Prospectus:**
- PDF
- Excel (.xlsx, .xls)
- Word (.docx, .doc)

### Upload Process

1. User selects file in admin modal
2. JavaScript validates file type and size
3. File is uploaded via FormData to respective API
4. API validates file again (server-side)
5. File is saved with unique filename
6. Database is updated with file path
7. Success response returned to client

### Error Handling

- Client-side validation for immediate feedback
- Server-side validation for security
- Proper HTTP status codes for different error types
- Detailed error messages for debugging

## Security Considerations

- File type validation by both extension and MIME type
- File size limits to prevent abuse
- Unique filenames to prevent overwrites
- Directory creation with proper permissions
- Database updates use prepared statements

## Database Integration

The upload APIs automatically update the database with file paths:

```sql
-- For images
UPDATE programs SET image_path = '../../assets/img/programs/BSIS-1643723456.jpg' WHERE id = 1;

-- For prospectus  
UPDATE programs SET prospectus_path = '../../assets/prospectus/BSIS-prospectus-1643723456.pdf' WHERE id = 1;
```

## Usage in Admin Interface

The admin modal now includes:
- File upload inputs for images and prospectus
- Manual path entry as fallback option
- Current file indicators
- File type and size hints
- Automatic database updates after successful uploads
