#!/bin/bash

echo "=========================================="
echo "  LostNest Installation Script"
echo "=========================================="

# Navigate to project directory
cd "$(dirname "$0")"

echo ""
echo "Step 1: Checking Python virtual environment..."
if [ -d "lostnest/bin" ]; then
    echo "✅ Virtual environment found"
else
    echo "❌ Virtual environment not found. Creating one..."
    python3 -m venv lostnest
fi

echo ""
echo "Step 2: Activating virtual environment..."
source lostnest/bin/activate

echo ""
echo "Step 3: Installing Python dependencies..."
pip install --upgrade pip
pip install -r requirements.txt

echo ""
echo "Step 4: Downloading YOLOv8 model..."
if [ -f "yolov8n.pt" ]; then
    echo "✅ YOLOv8 model already exists"
else
    python3 -c "from ultralytics import YOLO; YOLO('yolov8n.pt')"
    echo "✅ YOLOv8 model downloaded"
fi

echo ""
echo "Step 5: Setting up database..."
echo "Please run these SQL commands manually:"
echo "  mysql -u root -p lostnest < lostnest.sql"
echo "  mysql -u root -p lostnest < database_schema_update.sql"

echo ""
echo "Step 6: Setting folder permissions..."
chmod 777 uploads/lost-items
chmod 777 uploads/found-items
echo "✅ Permissions set"

echo ""
echo "=========================================="
echo "  Installation Complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Import database: mysql -u root -p lostnest < lostnest.sql"
echo "2. Update schema: mysql -u root -p lostnest < database_schema_update.sql"
echo "3. Start AI Matcher: ./start_ai_matcher.sh"
echo "4. Start XAMPP: sudo /opt/lampp/lampp start"
echo "5. Open browser: http://localhost/lostnest/"
echo ""
echo "For more info, see README.md"
echo "=========================================="
