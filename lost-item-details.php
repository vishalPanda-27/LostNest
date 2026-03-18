<?php
include("config.php");
$id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM lost_items WHERE id=$id");
$item = mysqli_fetch_assoc($result);
if (!$item) { die("Item not found"); }
$photos = explode(",", $item['photos']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($item['item_name']) ?> - Lost Item</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include("header.php"); ?>

<div class="details-box">
    <h2><?= htmlspecialchars($item['item_name']) ?></h2>
    <p><b>Category:</b> <?= htmlspecialchars($item['category']) ?></p>
    <p><b>Date Lost:</b> <?= htmlspecialchars($item['date_lost']) ?></p>
    <p><b>Place Lost:</b> <?= htmlspecialchars($item['place_lost']) ?></p>
    <p><b>Description:</b> <?= htmlspecialchars($item['description']) ?></p>
    <p><b>Document Type:</b> <?= htmlspecialchars($item['document_type']) ?></p>
    <p><b>Contact Number:</b> <?= htmlspecialchars($item['contact_number']) ?></p>
    <p><b>NFT ID:</b> <?= htmlspecialchars($item['nft_id']) ?></p>

    <div class="photos">
        <?php foreach ($photos as $photo): ?>
            <img src="uploads/<?= htmlspecialchars($photo) ?>" alt="Lost Photo">
        <?php endforeach; ?>
    </div>
</div>

<?php include("footer.php"); ?>
</body>
</html>
