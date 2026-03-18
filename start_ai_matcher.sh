#!/bin/bash

# LostNest AI Matcher Startup Script
echo "=========================================="
echo "  LostNest AI Image Matcher (YOLOv8)"
echo "=========================================="

# Navigate to project directory
cd "$(dirname "$0")"

# Activate virtual environment
echo "Activating virtual environment..."
source lostnest/bin/activate

# Check if YOLOv8 model exists
if [ ! -f "yolov8n.pt" ]; then
    echo "Downloading YOLOv8 model..."
    python3 -c "from ultralytics import YOLO; YOLO('yolov8n.pt')"
fi

# Start Flask server
echo "Starting AI Matcher Flask Server on http://127.0.0.1:5000"
echo "Press Ctrl+C to stop"
echo "=========================================="
python3 ai_matcher.py
