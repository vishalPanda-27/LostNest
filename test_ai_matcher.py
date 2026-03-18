#!/usr/bin/env python3
"""
Test script for LostNest AI Matcher
"""
import requests
import sys
import os

def test_health():
    """Test if Flask server is running"""
    try:
        response = requests.get("http://127.0.0.1:5000/health", timeout=5)
        if response.status_code == 200:
            print("✅ Flask server is running")
            print(f"   Response: {response.json()}")
            return True
        else:
            print(f"❌ Server returned status code: {response.status_code}")
            return False
    except requests.exceptions.ConnectionError:
        print("❌ Cannot connect to Flask server")
        print("   Please start the server with: ./start_ai_matcher.sh")
        return False
    except Exception as e:
        print(f"❌ Error: {e}")
        return False

def test_image_processing():
    """Test image processing endpoint"""
    # Find a test image
    test_dirs = [
        "uploads/lost-items/",
        "uploads/found-items/"
    ]
    
    test_image = None
    for dir_path in test_dirs:
        if os.path.exists(dir_path):
            files = [f for f in os.listdir(dir_path) if f.endswith(('.jpg', '.jpeg', '.png', '.webp'))]
            if files:
                test_image = os.path.abspath(os.path.join(dir_path, files[0]))
                break
    
    if not test_image:
        print("⚠️  No test images found in uploads folder")
        return False
    
    print(f"\n📸 Testing with image: {test_image}")
    
    try:
        data = {
            "image_path": test_image,
            "table": "lost_items",
            "item_id": "999"
        }
        response = requests.post("http://127.0.0.1:5000/process_image", data=data, timeout=30)
        
        if response.status_code == 200:
            result = response.json()
            print("✅ Image processing successful")
            print(f"   Match found: {result.get('match_found', False)}")
            if result.get('match_found'):
                print(f"   Similarity: {result.get('similarity', 0):.2%}")
                print(f"   Matched ID: {result.get('matched_id')}")
            return True
        else:
            print(f"❌ Processing failed with status: {response.status_code}")
            return False
    except Exception as e:
        print(f"❌ Error during processing: {e}")
        return False

def main():
    print("=" * 50)
    print("  LostNest AI Matcher - Test Suite")
    print("=" * 50)
    
    # Test 1: Health check
    print("\n[Test 1] Health Check")
    if not test_health():
        sys.exit(1)
    
    # Test 2: Image processing
    print("\n[Test 2] Image Processing")
    test_image_processing()
    
    print("\n" + "=" * 50)
    print("  Tests completed!")
    print("=" * 50)

if __name__ == "__main__":
    main()
