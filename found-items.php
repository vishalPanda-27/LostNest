<?php
include("config.php");
if (session_status() == PHP_SESSION_NONE) {
    // Session is not started, so start it
    session_start();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name      = $_POST['item_name'];
    $category       = $_POST['category'];
    $date_found     = $_POST['date_found'];
    $place_found    = $_POST['place_found'];
    $description    = $_POST['description'];
    $contact_number = $_POST['contact_number'];
    $photo1=uploadFile("photo1","uploads/found-items/");
    $photo2=uploadFile("photo2","uploads/found-items/");
    $photo3=uploadFile("photo3","uploads/found-items/");
    
    // Generate unique ID
    $nft_id = generateUniqueID('found_items');

    // Insert into DB
    $data = [
        "username"       => $_SESSION['username'],
        "item_name"      => $item_name,
        "category"       => $category,
        "date_found"     => $date_found,
        "place_found"    => $place_found,
        "description"    => $description,
        "photo1"         => $photo1,
        "photo2"         => $photo2,
        "photo3"         => $photo3,
        "contact_number" => $contact_number,
        "nft_id"         => $nft_id
    ];
    $id=insertInto("found_items", $data,true);

    // Process image matching with AI
    $photoFullPath = __DIR__ . "/uploads/found-items/" . $photo1;
    $matchResult = processImageMatch($photoFullPath, 'found_items', $id);

    // ✅ Pass values to template
    $date          = $date_found;
    $place         = $place_found;
    $extra_field   = null;
    $ownership_file = null;
    header("location:items-submit.php?id=$id&type=found&match=" . ($matchResult['match_found'] ?? 0));
    exit;
}
include 'header.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost Nest - Register Found Item</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <form action="found-items.php" method="POST" enctype="multipart/form-data">
        <div class="form-container">
            <h2>Upload Found Item Details</h2>

            <!-- Row 1 -->
            <div class="form-row">
                <input type="text" name="item_name" placeholder="Item Name" required>
                <input type="text" name="category" placeholder="Category" required>
            </div>

            <!-- Row 2 -->
            <div class="form-row">
                <input type="date" name="date_found" required>
                <input type="text" name="place_found" placeholder="Place Of Found" required>
            </div>

            <!-- Contact Number -->
            <div class="form-row">
                <input type="tel" name="contact_number" placeholder="Contact Number" required>
            </div>

            <!-- Photos -->
            <div class="photo-upload">
                <label class="photo-box">
                    Add photo
                    <input type="file" name="photo1" accept="image/*" hidden>
                </label>
                <label class="photo-box">
                    Add photo
                    <input type="file" name="photo2" accept="image/*" hidden>
                </label>
                <label class="photo-box">
                    Add photo
                    <input type="file" name="photo3" accept="image/*" hidden>
                </label>
            </div>

            <!-- Description -->
            <textarea name="description" placeholder="Detailed Description..." required></textarea>

            <div class="form-row">
                <button type="submit" class="submit-btn">Submit</button>
            </div>
        </div>
    </form>

    <?php include 'footer.php'; ?>
</body>
</html>
