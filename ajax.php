<?php
session_start();
include("config.php");
$action = $_POST['action'] ?? '';
if ($action === "signup") {
    $fullname = $_POST['full_name'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($fullname == '' || $dob == '' || $username == '' || $password == '') {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit;
    }

    $data = [
        "full_name" => $fullname,
        "dob" => $dob,
        "username" => $username,
        "password" => $password // plain text for now
    ];

    $result = insertInto("users", $data);

    if ($result) {
        echo json_encode(["success" => true, "message" => "User registered successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Signup failed"]);
    }
    exit;
}
if ($action === "signin") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username == '' || $password == '') {
        echo json_encode(["success" => false, "message" => "Enter both username and password"]);
        exit;
    }

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' LIMIT 1");
    if (!$query || mysqli_num_rows($query) == 0) {
        echo json_encode(["success" => false, "message" => "User not found"]);
        exit;
    }
    $row = mysqli_fetch_assoc($query);
    if ($password == $row['password']) {  // ❗ plain text check for now
        $_SESSION['username'] = $row['username'];
        $_SESSION['full_name'] = $row['full_name'];
        $_SESSION['profile_photo'] = $row['profile_photo'];
        echo json_encode(["success" => true, "message" => "Welcome $row[full_name]"]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid password"]);
    }
    exit;
}
if ($action === "checkItem") {
    $itemName = $_POST['item_name'] ?? '';
    $category = $_POST['category'] ?? '';

    if ($itemName == '' || $category == '') {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit;
    }

    // Search lost-items table (simple LIKE search for now)
    $sql = "SELECT * FROM lost_items WHERE category LIKE '%$category%' OR item_name LIKE '%$itemName%' LIMIT 6";
    $result = mysqli_query($conn, $sql);

    $items = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = [
                "id" => $row['id'],
                "photo" => $row['photo'] // assuming 1 main photo column
            ];
        }
    }

    echo json_encode([
        "success" => true,
        "items" => $items
    ]);
    exit;
}
echo json_encode(["success" => false, "message" => "Invalid request"]);
?>
