#!/usr/bin/env python3
"""
LostNest System Status Checker
"""
import requests
import mysql.connector
import os
from datetime import datetime

def check_ai_matcher():
    """Check if AI Matcher Flask server is running"""
    try:
        response = requests.get("http://127.0.0.1:5000/health", timeout=3)
        if response.status_code == 200:
            data = response.json()
            return True, f"Running - Model: {data.get('model', 'unknown')}"
        return False, f"HTTP {response.status_code}"
    except requests.exceptions.ConnectionError:
        return False, "Not running - Start with ./start_ai_matcher.sh"
    except Exception as e:
        return False, str(e)

def check_database():
    """Check database connection and stats"""
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="lostnest"
        )
        cursor = conn.cursor()
        
        # Get counts
        cursor.execute("SELECT COUNT(*) FROM lost_items")
        lost_count = cursor.fetchone()[0]
        
        cursor.execute("SELECT COUNT(*) FROM found_items")
        found_count = cursor.fetchone()[0]
        
        cursor.execute("SELECT COUNT(*) FROM lost_items WHERE match_found=1")
        lost_matched = cursor.fetchone()[0]
        
        cursor.execute("SELECT COUNT(*) FROM found_items WHERE match_found=1")
        found_matched = cursor.fetchone()[0]
        
        cursor.close()
        conn.close()
        
        return True, {
            "lost_items": lost_count,
            "found_items": found_count,
            "lost_matched": lost_matched,
            "found_matched": found_matched,
            "total_matches": lost_matched + found_matched
        }
    except Exception as e:
        return False, str(e)

def check_uploads():
    """Check upload directories"""
    dirs = {
        "lost-items": "uploads/lost-items/",
        "found-items": "uploads/found-items/"
    }
    
    results = {}
    for name, path in dirs.items():
        if os.path.exists(path):
            files = [f for f in os.listdir(path) if os.path.isfile(os.path.join(path, f))]
            results[name] = {
                "exists": True,
                "writable": os.access(path, os.W_OK),
                "file_count": len(files)
            }
        else:
            results[name] = {"exists": False}
    
    return results

def check_model():
    """Check if YOLOv8 model exists"""
    return os.path.exists("yolov8n.pt")

def main():
    print("=" * 60)
    print("  LostNest System Status")
    print("  " + datetime.now().strftime("%Y-%m-%d %H:%M:%S"))
    print("=" * 60)
    
    # Check AI Matcher
    print("\n[1] AI Matcher (Flask Server)")
    status, info = check_ai_matcher()
    if status:
        print(f"    ✅ {info}")
    else:
        print(f"    ❌ {info}")
    
    # Check Database
    print("\n[2] Database Connection")
    status, info = check_database()
    if status:
        print(f"    ✅ Connected")
        print(f"    📊 Statistics:")
        print(f"       - Lost Items: {info['lost_items']} ({info['lost_matched']} matched)")
        print(f"       - Found Items: {info['found_items']} ({info['found_matched']} matched)")
        print(f"       - Total Matches: {info['total_matches']}")
    else:
        print(f"    ❌ {info}")
    
    # Check YOLOv8 Model
    print("\n[3] YOLOv8 Model")
    if check_model():
        print("    ✅ yolov8n.pt found")
    else:
        print("    ❌ Model not found - Run: python3 -c 'from ultralytics import YOLO; YOLO(\"yolov8n.pt\")'")
    
    # Check Upload Directories
    print("\n[4] Upload Directories")
    uploads = check_uploads()
    for name, info in uploads.items():
        if info.get("exists"):
            writable = "✅" if info.get("writable") else "❌"
            print(f"    {writable} {name}: {info.get('file_count', 0)} files")
        else:
            print(f"    ❌ {name}: Directory not found")
    
    print("\n" + "=" * 60)
    print("  Status Check Complete")
    print("=" * 60)

if __name__ == "__main__":
    main()
