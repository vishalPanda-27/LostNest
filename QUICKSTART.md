# 🚀 LostNest - Quick Start Guide

## ⚡ Fast Setup (5 Minutes)

### 1️⃣ Database Setup
```bash
# Create database and import schema
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS lostnest;"
mysql -u root -p lostnest < lostnest.sql
mysql -u root -p lostnest < database_schema_update.sql
```

### 2️⃣ Install Dependencies
```bash
# Run the installation script
./install.sh
```

### 3️⃣ Start AI Matcher
```bash
# Start the Flask AI server
./start_ai_matcher.sh
```
Keep this terminal open! The AI matcher needs to run continuously.

### 4️⃣ Start Web Server
```bash
# Start XAMPP/LAMPP
sudo /opt/lampp/lampp start
```

### 5️⃣ Access Application
Open browser: **http://localhost/lostnest/**

---

## ✅ Verify Installation

### Test AI Matcher
```bash
# In a new terminal
source lostnest/bin/activate
python3 test_ai_matcher.py
```

Expected output:
```
✅ Flask server is running
✅ Image processing successful
```

### Test Web Interface
1. Login with existing user or create new account
2. Upload a lost item with photo
3. Upload a found item with similar photo
4. Check "My Matches" page

---

## 🎯 How to Use

### Report Lost Item
1. Click **"Lost Items"** in navigation
2. Fill in item details
3. Upload 1-3 photos (clear, well-lit)
4. Upload ownership proof (bill, receipt, etc.)
5. Submit

### Report Found Item
1. Click **"Found Items"** in navigation
2. Fill in item details
3. Upload 1-3 photos
4. Submit

### Check Matches
- Go to **"My Matches"** page
- See all matched items with contact information
- Contact the other party directly

---

## 🔧 Troubleshooting

### AI Matcher Not Running
```bash
# Check status
curl http://127.0.0.1:5000/health

# If not running, start it
./start_ai_matcher.sh
```

### Database Connection Error
```bash
# Check MySQL is running
sudo /opt/lampp/lampp status

# Restart if needed
sudo /opt/lampp/lampp restart
```

### Images Not Uploading
```bash
# Fix permissions
chmod 777 uploads/lost-items
chmod 777 uploads/found-items
```

### Low Match Accuracy
Edit `ai_matcher.py` line 67:
```python
threshold = 0.70  # Lower = stricter (0.60), Higher = lenient (0.80)
```

---

## 🎨 Features

✅ **AI-Powered Matching** - YOLOv8 deep learning  
✅ **Automatic NFT IDs** - Unique identifiers  
✅ **Real-time Processing** - Instant match detection  
✅ **Multi-feature Analysis** - Objects, colors, edges  
✅ **User Dashboard** - Track all matches  
✅ **Secure** - SQL injection protection  

---

## 📊 System Status

### Check AI Matcher
```bash
curl http://127.0.0.1:5000/health
```

### Check Database
```bash
mysql -u root -p lostnest -e "SELECT COUNT(*) FROM lost_items; SELECT COUNT(*) FROM found_items;"
```

### View Matches
```bash
mysql -u root -p lostnest -e "SELECT * FROM lost_items WHERE match_found=1;"
```

---

## 🔄 Auto-Start AI Matcher (Optional)

To run AI matcher as a system service:

```bash
# Copy service file
sudo cp lostnest-ai.service /etc/systemd/system/

# Enable and start
sudo systemctl enable lostnest-ai
sudo systemctl start lostnest-ai

# Check status
sudo systemctl status lostnest-ai
```

---

## 📝 Important Notes

1. **Keep AI Matcher Running**: The Flask server must be running for image matching
2. **Upload Quality Photos**: Clear, well-lit images improve matching accuracy
3. **Multiple Angles**: Use all 3 photo slots for better results
4. **Threshold Tuning**: Adjust similarity threshold based on your needs

---

## 🆘 Need Help?

- Check logs: `tail -f /opt/lampp/logs/error_log`
- Test AI: `python3 test_ai_matcher.py`
- Read full docs: `README.md`

---

## 🎉 You're Ready!

The system is now fully functional. Upload items and watch the AI match them automatically!

**Default Test User** (if exists):
- Username: `ram`
- Password: `ram`

---

**Version**: 1.0.0  
**AI Model**: YOLOv8n (Ultralytics)  
**License**: Educational Use
