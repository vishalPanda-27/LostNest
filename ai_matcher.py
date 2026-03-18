from flask import Flask, request, jsonify
from ultralytics import YOLO
import cv2
import numpy as np
from sklearn.metrics.pairwise import cosine_similarity
import mysql.connector
import os
import torch
import hashlib

app = Flask(__name__)

# Load YOLOv8 model for feature extraction
model = YOLO('yolov8n.pt')
device = 'cuda' if torch.cuda.is_available() else 'cpu'

# Database connection
def get_db():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="lostnest"
    )

def extract_features(image_path):
    """Extract deep features using YOLOv8 backbone"""
    img = cv2.imread(image_path)
    if img is None:
        return None
    
    # Resize for consistency
    img = cv2.resize(img, (640, 640))
    
    # Get YOLOv8 predictions and extract deep features
    results = model.predict(img, verbose=False, device=device)
    
    # Extract multiple feature types
    feature_vector = []
    
    # 1. Object detection features
    for result in results:
        if result.boxes is not None and len(result.boxes) > 0:
            boxes = result.boxes.xyxy.cpu().numpy()
            confs = result.boxes.conf.cpu().numpy()
            classes = result.boxes.cls.cpu().numpy()
            
            # Normalize box coordinates
            for box, conf, cls in zip(boxes, confs, classes):
                normalized_box = box / 640.0
                feature_vector.extend(normalized_box)
                feature_vector.append(conf)
                feature_vector.append(cls)
    
    # 2. Color histogram features
    hsv = cv2.cvtColor(img, cv2.COLOR_BGR2HSV)
    hist_h = cv2.calcHist([hsv], [0], None, [32], [0, 180])
    hist_s = cv2.calcHist([hsv], [1], None, [32], [0, 256])
    hist_v = cv2.calcHist([hsv], [2], None, [32], [0, 256])
    
    color_features = np.concatenate([hist_h.flatten(), hist_s.flatten(), hist_v.flatten()])
    color_features = color_features / (color_features.sum() + 1e-7)  # Normalize
    
    # 3. Edge features
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    edges = cv2.Canny(gray, 100, 200)
    edge_density = np.sum(edges > 0) / (640 * 640)
    
    # Combine all features
    if len(feature_vector) == 0:
        feature_vector = color_features.tolist()
    else:
        feature_vector.extend(color_features.tolist())
    
    feature_vector.append(edge_density)
    
    return np.array(feature_vector, dtype=np.float32)

def find_similar_image(features, table, current_id=None):
    """Find similar images in database using advanced matching"""
    db = get_db()
    cursor = db.cursor(dictionary=True)
    
    opposite_table = "found_items" if table == "lost_items" else "lost_items"
    
    cursor.execute(f"SELECT id, photo1, nft_id, match_found FROM {opposite_table} WHERE match_found = 0")
    items = cursor.fetchall()
    
    best_match = None
    best_similarity = 0.0
    threshold = 0.70  # Adjusted threshold
    
    for item in items:
        if item['photo1']:
            img_path = f"uploads/{opposite_table.replace('_', '-')}/{item['photo1']}"
            if os.path.exists(img_path):
                stored_features = extract_features(img_path)
                if stored_features is not None:
                    # Ensure same length by padding with zeros
                    max_len = max(len(features), len(stored_features))
                    f1 = np.pad(features, (0, max_len - len(features)), 'constant')
                    f2 = np.pad(stored_features, (0, max_len - len(stored_features)), 'constant')
                    
                    # Calculate cosine similarity
                    f1 = f1.reshape(1, -1)
                    f2 = f2.reshape(1, -1)
                    similarity = cosine_similarity(f1, f2)[0][0]
                    
                    if similarity > best_similarity and similarity >= threshold:
                        best_similarity = similarity
                        best_match = item
    
    cursor.close()
    db.close()
    
    return best_match, best_similarity

@app.route('/process_image', methods=['POST'])
def process_image():
    """Process uploaded image and find matches"""
    try:
        image_path = request.form.get('image_path')
        table = request.form.get('table')
        item_id = request.form.get('item_id')
        
        if not os.path.exists(image_path):
            return jsonify({"success": False, "error": "Image not found"})
        
        # Extract features
        features = extract_features(image_path)
        if features is None:
            return jsonify({"success": False, "error": "Feature extraction failed"})
        
        # Find similar image
        match, similarity = find_similar_image(features, table, item_id)
        
        if match:
            # Generate shared NFT ID from both items
            db = get_db()
            cursor = db.cursor()
            
            # Use the existing NFT ID from the matched item
            shared_nft_id = match['nft_id']
            
            # Update both items
            cursor.execute(f"UPDATE {table} SET nft_id=%s, match_found=1 WHERE id=%s", (shared_nft_id, item_id))
            
            opposite_table = "found_items" if table == "lost_items" else "lost_items"
            cursor.execute(f"UPDATE {opposite_table} SET match_found=1 WHERE id=%s", (match['id'],))
            
            db.commit()
            cursor.close()
            db.close()
            
            return jsonify({
                "success": True,
                "match_found": True,
                "matched_id": match['id'],
                "similarity": float(similarity),
                "nft_id": shared_nft_id
            })
        
        return jsonify({
            "success": True,
            "match_found": False
        })
        
    except Exception as e:
        return jsonify({"success": False, "error": str(e)})

@app.route('/health', methods=['GET'])
def health():
    """Health check endpoint"""
    return jsonify({"status": "running", "model": "yolov8n"})

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000, debug=True)
