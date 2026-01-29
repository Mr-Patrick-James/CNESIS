# Directory Setup for Uploaded Files

## New Directory Structure

```
CNESIS/
├── assets/
│   ├── img/                    # Predefined program images (existing)
│   │   └── programs/           # Keep existing predefined images here
│   └── uploads/                # NEW: Safe location for user uploads
│       └── programs/           # Organized by content type
│           ├── images/         # Program images uploaded by users
│           └── prospectus/      # Prospectus files uploaded by users
```

## Manual Setup Instructions

Create these directories manually:

```bash
# Create the new upload directories
mkdir -p assets/uploads/programs/images
mkdir -p assets/uploads/programs/prospectus

# Set permissions (Linux/Mac)
chmod 755 assets/uploads
chmod 755 assets/uploads/programs
chmod 755 assets/uploads/programs/images
chmod 755 assets/uploads/programs/prospectus

# For WAMP on Windows, ensure web server has write permissions
```

## Benefits of New Structure

### 📁 **Separation of Concerns**
- **Predefined Images**: `assets/img/programs/` - System default images
- **User Uploads**: `assets/uploads/programs/` - User uploaded content

### 🔒 **Better Security**
- Isolated upload directory
- Easier to set permissions
- Separate from system assets

### 🗂️ **Better Organization**
- Clear distinction between system and user content
- Organized by file type (images vs documents)
- Easier to backup and manage

### 🚀 **Scalability**
- Can easily add more upload types
- Clear structure for future expansion
- Better for CDN integration

## File Path Examples

### **Program Images**
- **Old**: `../../assets/img/programs/BSIS-1643723456.jpg`
- **New**: `../../assets/uploads/programs/images/BSIS-1643723456.jpg`

### **Prospectus Files**
- **Old**: `../../assets/prospectus/BPA-prospectus-1643723456.pdf`
- **New**: `../../assets/uploads/programs/prospectus/BPA-prospectus-1643723456.pdf`

## Migration Notes

1. **Existing Files**: Current files in old directories will still work
2. **New Uploads**: Will go to new directories automatically
3. **Database**: No changes needed - paths are handled automatically
4. **Display**: User page will show images from both locations

## API Updates

The upload APIs have been updated to use the new directory structure:

- `upload-image.php` → `assets/uploads/programs/images/`
- `upload-prospectus.php` → `assets/uploads/programs/prospectus/`

## Default Images

Default program images should remain in:
`assets/img/default-program.jpg`

This ensures that:
- System defaults are separate from user uploads
- Easy to maintain default images
- Clear distinction between content types
