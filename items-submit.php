<?php
// Expected variables:
// $_GET["type"]           = "Lost" or "Found"
// $item_name
// $category
// $date
// $place
// $description
// $contact_number
// $nft_id
// $extra_field (like document_type for Lost, null for Found)
// $ownership_file (for Lost, null for Found)
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Lost Nest - <?= htmlspecialchars($_GET["type"]) ?> Item Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #EAF4FB;
            padding: 20px;
        }

        .details-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .details-box h2 {
            color: #004A99;
        }

        .photos {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .photos img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #004A99;
        }

        .finish-btn {
            padding: 10px 20px;
            background: #004A99;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php include "header.php";
    include_once "config.php";
    ?>
    <?php
    if ($_GET["type"] == "found")
        $sql = "Select * from found_items where id=$_GET[id]";
    else
        $sql = "Select * from lost_items where id=$_GET[id]";
    $rs = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($rs);
    
    // Check if match was found
    $matchFound = isset($_GET['match']) && $_GET['match'] == 1;
    ?>

    <!-- First Details Section -->
    <div class="details-box">
        <?php if ($matchFound): ?>
        <div style="background: #4CAF50; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
            <h3 style="margin: 0; color: white;">🎉 MATCH FOUND!</h3>
            <p style="margin: 10px 0 0 0;">This item has been matched with a <?= $_GET["type"] == "lost" ? "found" : "lost" ?> item in our database!</p>
        </div>
        <?php endif; ?>
        <h2><?= htmlspecialchars($_GET["type"]) ?> Item Submission</h2>
        <p><b>Item Name:</b> <?= htmlspecialchars($row["item_name"]) ?></p>
        <p><b>Item Category:</b> <?= htmlspecialchars($row["category"]) ?></p>
        <p><b>Date <?= $_GET["type"] ?>:</b> <?= htmlspecialchars($row["date_" . $_GET["type"]]) ?></p>
        <p><b>Place <?= $_GET["type"] ?>:</b> <?= htmlspecialchars($row["place_" . $_GET["type"]]) ?></p>
        <p><b>Detailed Description:</b> <?= htmlspecialchars($row["description"]) ?></p>

        <?php if ($_GET["type"] === "lost"): ?>
            <p><b>Document Type:</b> <?= htmlspecialchars($row["document_type"]) ?></p>
            <p><b>Uploaded File:</b> <?= htmlspecialchars($row["ownership_file"]) ?></p>
        <?php endif; ?>

        <p><b>Contact Number:</b> <?= htmlspecialchars($row["contact_number"]) ?></p>
        <p><b>NFT ID (Transaction Hash):</b> <?= htmlspecialchars($row["nft_id"]) ?></p>
        <p><b>photo1:</b> <?= htmlspecialchars($row["photo1"]) ?></p>
        <p><b>photo2:</b> <?= htmlspecialchars($row["photo2"]) ?></p>
        <p><b>photo3:</b> <?= htmlspecialchars($row["photo3"]) ?></p>
    </div>

    <!-- Photos Section -->
    <div class="details-box">
        <h2>Uploaded Photos</h2>
        <div class="photos">
            <img src="uploads/<?= htmlspecialchars($_GET['type']) ?>-items/<?= htmlspecialchars($row['photo1']) ?>" 
     alt="Uploaded Photo">
            <img src="uploads/<?= $_GET["type"] ?>-items/<?= $row["photo2"] ?>" alt="Uploaded Photo">
            <img src="uploads/<?= $_GET["type"] ?>-items/<?= $row["photo3"] ?>" alt="Uploaded Photo">
        </div>
        <br>
        <button class="finish-btn" onclick="window.location.href='index.php'">Finish</button>
    </div>

    <?php include "footer.php"; ?>
</body>

</html>