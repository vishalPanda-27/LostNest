<?php
$conn = mysqli_connect("localhost", "root", "", "lostnest");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// ====================
// SQL Functions
// ====================
function update($table, $dataArray, $cond) {
    global $conn;
    $set = "";
    $type = "";
    $values = [];

    foreach ($dataArray as $key => $value) {
        $set .= "$key=?,";
        $type .= "s"; // all strings for now
        $values[] = $value;
    }
    $set = rtrim($set, ",");

    // 👇 make sure WHERE clause is appended correctly
    $sql = "UPDATE $table SET $set WHERE $cond";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        return "SQL Error: " . mysqli_error($conn) . " | Query: " . $sql;
    }

    mysqli_stmt_bind_param($stmt, $type, ...$values);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return 1;
    } else {
        mysqli_stmt_close($stmt);
        return 0;
    }
}

function insertInto($table, $dataArray,$isAuto=false)
{
    global $conn;
    $type = "";
    $fields = "";
    $v = "";
    $values = array();
    foreach ($dataArray as $key => $value) {
        $type .= "s";
        $fields .= "$key,";
        $v .= "?,";
        $values[] = $value;
    }
    $fields = rtrim($fields, ",");
    $v = rtrim($v, ",");
    $sql = "INSERT INTO $table ($fields) VALUES ($v)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        echo  mysqli_error($conn);
    }
    mysqli_stmt_bind_param($stmt, $type, ...$values);
    if (mysqli_stmt_execute($stmt)) {
        return  $isAuto?$conn->insert_id:1;
    } else {
        return 0;
    }
}
// ====================
// Unique ID Generator
// ====================

function generateUniqueID($table = 'lost_items')
{
    // Generate unique ID format: LN-YYYYMMDD-HHMMSS-RANDOM
    $prefix = ($table === 'lost_items') ? 'LOST' : 'FOUND';
    $timestamp = date('YmdHis');
    $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
    
    return $prefix . '-' . $timestamp . '-' . $random;
}





function processImageMatch($photoPath, $table, $item_id)
{
    // Check if Flask server is running
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:5000/health");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    $health = curl_exec($ch);
    curl_close($ch);
    
    if (!$health) {
        error_log("AI Matcher Flask server is not running. Please start it with: python3 ai_matcher.py");
        return null;
    }
    
    $curl = curl_init();
    $fields = [
        "image_path" => $photoPath,
        "table" => $table,
        "item_id" => $item_id
    ];

    curl_setopt_array($curl, [
        CURLOPT_URL => "http://127.0.0.1:5000/process_image",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $fields,
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        error_log("AI Matcher Error: " . $err);
        return null;
    }

    return json_decode($response, true);
}




// Allowed file extensions & max size (5 MB)
$ALLOWED_EXTENSIONS = ["jpg", "jpeg", "png", "gif", "pdf", "webp"];
$MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB

function uploadFile($fileField, $target_dir = "uploads/") {
    global $ALLOWED_EXTENSIONS, $MAX_FILE_SIZE;

    if (!isset($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== 0) {
        return ""; // ❌ No file
    }

    $file_tmp  = $_FILES[$fileField]['tmp_name'];
    $file_name = basename($_FILES[$fileField]['name']);
    $file_size = $_FILES[$fileField]['size'];
    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Validate extension
    if (!in_array($file_ext, $ALLOWED_EXTENSIONS)) {
        return "";
    }

    // Validate size
    if ($file_size > $MAX_FILE_SIZE) {
        return "";
    }

    // Ensure target dir exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Rename file → timestamp + extension
    $filename = time() . "_" . $file_name;
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($file_tmp, $target_file)) {
        return $filename;  // ✅ Only filename returned
    }
    return "";
}


?>

