# LostNest - AI-Powered Lost & Found System

## Overview
LostNest is a fully functional lost and found image matching system using YOLOv8 AI for intelligent image processing. When users upload images of lost or found items, the system automatically matches them using deep learning and assigns unique NFT IDs.

## Features
✅ **AI-Powered Image Matching** - YOLOv8 deep learning model
✅ **Smart Feature Extraction** - Object detection, color histograms, edge detection
✅ **Real-time Matching** - Instant notification when items match
✅ **User Dashboard** - View all matched items
✅ **Secure Database** - MySQL with proper indexing

## System Requirements
- PHP 7.1+
- MySQL/MariaDB
- Python 3.8+
- XAMPP/LAMPP
- 2GB+ RAM (for YOLOv8)

## Installation & Setup

### 1. Database Setup
```bash
# Import the database schema
mysql -u root -p lostnest < lostnest.sql

# Apply the schema updates
mysql -u root -p lostnest < database_schema_update.sql
```

### 2. Python Environment Setup
```bash
# The virtual environment is already created in lostnest/ folder
# Activate it:
source lostnest/bin/activate

# Install/Update dependencies:
pip install -r requirements.txt
```

### 3. Start the AI Matcher Server
```bash
# Option 1: Use the startup script (recommended)
./start_ai_matcher.sh

# Option 2: Manual start
source lostnest/bin/activate
python3 ai_matcher.py
```

The Flask server will start on `http://127.0.0.1:5000`

### 4. Start XAMPP/LAMPP
```bash
sudo /opt/lampp/lampp start
```

### 5. Access the Application
Open browser: `http://localhost/lostnest/`

## How It Works

### Image Upload Flow:
1. **User uploads lost/found item** with photos
2. **NFT ID is generated** for the item
3. **AI processes the image**:
   - YOLOv8 detects objects
   - Extracts color histograms
   - Analyzes edge features
   - Creates feature vector
4. **Matching algorithm runs**:
   - Compares with opposite table (lost vs found)
   - Uses cosine similarity (threshold: 0.70)
   - Finds best match
5. **If match found**:
   - Both items get same NFT ID
   - `match_found` flag set to 1
   - User redirected with match notification

### AI Feature Extraction:
- **Object Detection**: YOLOv8 identifies objects and their positions
- **Color Features**: HSV histogram (96 dimensions)
- **Edge Features**: Canny edge detection density
- **Normalization**: All features normalized for consistent comparison

## File Structure
```
lostnest/
├── ai_matcher.py              # Flask AI server (YOLOv8)
├── start_ai_matcher.sh        # Startup script
├── config.php                 # Database & helper functions
├── lost-items.php             # Lost item submission
├── found-items.php            # Found item submission
├── items-submit.php           # Submission confirmation
├── my-matches.php             # User's matched items
├── lost-registry.php          # View all lost items
├── found-registry.php         # View all found items
├── lostnest/                  # Python virtual environment
├── uploads/
│   ├── lost-items/           # Lost item photos
│   └── found-items/          # Found item photos
└── yolov8n.pt                # YOLOv8 model weights
```

## API Endpoints

### Flask AI Matcher Server

#### Health Check
```
GET http://127.0.0.1:5000/health
Response: {"status": "running", "model": "yolov8n"}
```

#### Process Image
```
POST http://127.0.0.1:5000/process_image
Parameters:
  - image_path: Absolute path to image
  - table: "lost_items" or "found_items"
  - item_id: Database ID of the item

Response (Match Found):
{
  "success": true,
  "match_found": true,
  "matched_id": 5,
  "similarity": 0.87,
  "nft_id": "NFT-12345"
}

Response (No Match):
{
  "success": true,
  "match_found": false
}
```

## Database Schema

### lost_items
- id, username, item_name, category
- date_lost, place_lost, description
- contact_number, nft_id
- photo1, photo2, photo3
- document_type, ownership_file
- match_found (0 or 1)
- embedding (for future use)

### found_items
- id, username, item_name, category
- date_found, place_found, description
- contact_number, nft_id
- photo1, photo2, photo3
- match_found (0 or 1)
- embedding (for future use)

## Usage Guide

### For Users:

1. **Report Lost Item**:
   - Go to "Lost Items" page
   - Fill in details and upload photos
   - Upload ownership proof
   - Submit

2. **Report Found Item**:
   - Go to "Found Items" page
   - Fill in details and upload photos
   - Submit

3. **Check Matches**:
   - Visit "My Matches" page
   - See all matched items with contact info

### For Administrators:

1. **Monitor AI Server**:
```bash
# Check if running
curl http://127.0.0.1:5000/health

# View logs
tail -f /opt/lampp/htdocs/lostnest/ai_matcher.log
```

2. **Database Queries**:
```sql
-- View all matches
SELECT * FROM lost_items WHERE match_found = 1;
SELECT * FROM found_items WHERE match_found = 1;

-- View items by NFT ID
SELECT * FROM lost_items WHERE nft_id = 'NFT-12345';
```

## Troubleshooting

### AI Matcher Not Working
```bash
# Check if Flask server is running
curl http://127.0.0.1:5000/health

# Restart the server
./start_ai_matcher.sh
```

### Database Connection Error
- Check MySQL is running: `sudo /opt/lampp/lampp status`
- Verify credentials in config.php

### Image Upload Issues
- Check folder permissions: `chmod 777 uploads/lost-items uploads/found-items`
- Verify file size limits in php.ini

### Low Matching Accuracy
- Adjust threshold in ai_matcher.py (line 67)
- Current: 0.70 (70% similarity)
- Lower = stricter, Higher = more lenient

## Performance Optimization

### For Better Matching:
1. Upload clear, well-lit photos
2. Multiple angles help (use all 3 photo slots)
3. Focus on distinctive features

### Server Performance:
- YOLOv8 uses GPU if available (CUDA)
- Falls back to CPU automatically
- Average processing time: 2-5 seconds per image

## Security Notes
- All file uploads are validated
- SQL injection protection with prepared statements
- Session management for user authentication
- File type restrictions enforced

## Future Enhancements
- [ ] Email notifications on match
- [ ] Mobile app integration
- [ ] Advanced filtering options
- [ ] Multi-language support
- [ ] Real blockchain NFT integration

## Support
For issues or questions, check the logs:
- PHP errors: `/opt/lampp/logs/error_log`
- AI Matcher: Check terminal output

## License
This project is for educational purposes.

---
**Version**: 1.0.0  
**Last Updated**: 2025  
**AI Model**: YOLOv8n (Ultralytics)
# LostNest
