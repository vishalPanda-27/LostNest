<?php 
include("config.php");
if (session_status() == PHP_SESSION_NONE) {
    // Session is not started, so start it
    session_start();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name      = $_POST['item_name'];
    $category       = $_POST['category'];
    $date_lost      = $_POST['date_lost'];
    $place_lost     = $_POST['place_lost'];
    $description    = $_POST['description'];
    $document_type  = $_POST['document_type'];
    $contact_number = $_POST['contact_number'];
    $photo1=uploadFile("photo1","uploads/lost-items/");
    $photo2=uploadFile("photo2","uploads/lost-items/");
    $photo3=uploadFile("photo3","uploads/lost-items/");
    $ownership_file=uploadFile("ownership_file","uploads/lost-items/");
    
    // Generate unique ID
    $nft_id = generateUniqueID('lost_items');

    // Insert into DB
    $data = [
        "username"       => $_SESSION['username'],
        "item_name"      => $item_name,
        "category"       => $category,
        "date_lost"      => $date_lost,
        "place_lost"     => $place_lost,
        "description"    => $description,
        "document_type"  => $document_type,
        "ownership_file" => $ownership_file,
        "photo1"         => $photo1,
        "photo2"         => $photo2,
        "photo3"         => $photo3,
        "contact_number" => $contact_number,
        "nft_id"         => $nft_id
    ];
    $id=insertInto("lost_items", $data,true);
    
    // Process image matching with AI
    $photoFullPath = __DIR__ . "/uploads/lost-items/" . $photo1;
    $matchResult = processImageMatch($photoFullPath, 'lost_items', $id);
    
    // ✅ Pass values to template
    header("location:items-submit.php?id=$id&type=lost&match=" . ($matchResult['match_found'] ?? 0));
    exit;
}
include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost Nest - Register Lost Item</title>
    <link rel="stylesheet" href="style.css"> <!-- your common CSS -->
</head>

<body>

    <!-- Include the header -->
    <form action="lost-items.php" method="POST" enctype="multipart/form-data">
        <div class="form-container">
            <h2>Upload Lost Item Details</h2>

            <!-- Row 1 -->
            <div class="form-row">
                <input type="text" name="item_name" placeholder="Item Name" required>
                <input type="text" name="category" placeholder="Category" required>
            </div>

            <!-- Row 2 -->
            <div class="form-row">
                <input type="date" name="date_lost" placeholder="Date Of Lost" required>
                <input type="text" name="place_lost" placeholder="Place Of Lost" required>
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
            <!-- ✅ Ownership Proof Section -->
            <h3 style="color:#004A99; margin-top:20px;">Ownership Proof</h3>
            <div class="form-row">
                <input type="text" name="document_type" placeholder="Write Document type (e.g., Bill, Warranty, ID)" required>
            </div>
            <div class="form-row">
                <label class="custom-file-upload">
                    <input type="file" id="ownership_file" name="ownership_file" accept=".jpg,.png,.pdf" required>
                    Upload Ownership Proof
                </label>
                <span id="file-name">No file chosen</span>
            </div>

            <div class="form-row">
                <input type="tel" name="contact_number" placeholder="Contact Number" required>
                <button type="submit" class="submit-btn">Submit</button>
            </div>
        </div>
    </form>


    <!-- Include the footer -->
    <?php include 'footer.php'; ?>
    <script>
        const fileInput = document.getElementById("ownership_file");
        const fileName = document.getElementById("file-name");

        fileInput.addEventListener("change", () => {
            if (fileInput.files.length > 0) {
                fileName.textContent = fileInput.files[0].name;
            } else {
                fileName.textContent = "No file chosen";
            }
        });
    </script>

</body>

</html>