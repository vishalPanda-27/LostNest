<?php
include "config.php";
include "header.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit;
}

$username = $_SESSION['username'];

// Get matched lost items
$sql_lost = "SELECT l.*, f.id as found_id, f.item_name as found_item_name, f.contact_number as found_contact 
             FROM lost_items l 
             LEFT JOIN found_items f ON l.nft_id = f.nft_id AND l.match_found = 1 AND f.match_found = 1
             WHERE l.username = '$username' AND l.match_found = 1";
$result_lost = $conn->query($sql_lost);

// Get matched found items
$sql_found = "SELECT f.*, l.id as lost_id, l.item_name as lost_item_name, l.contact_number as lost_contact 
              FROM found_items f 
              LEFT JOIN lost_items l ON f.nft_id = l.nft_id AND f.match_found = 1 AND l.match_found = 1
              WHERE f.username = '$username' AND f.match_found = 1";
$result_found = $conn->query($sql_found);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Matches - Lost Nest</title>
    <style>
        body {
            background: #EAF4FB;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        h2 {
            color: #004A99;
            margin-bottom: 20px;
        }
        .match-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            border-left: 5px solid #4CAF50;
        }
        .match-header {
            background: #4CAF50;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .match-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .item-section {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .item-section h3 {
            color: #004A99;
            margin-top: 0;
        }
        .item-section img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
        }
        .contact-info {
            background: #FFF3CD;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            border-left: 3px solid #FFC107;
        }
        .no-matches {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🎉 My Matched Items</h2>
        
        <?php if ($result_lost->num_rows > 0 || $result_found->num_rows > 0): ?>
            
            <?php if ($result_lost->num_rows > 0): ?>
                <h3 style="color: #004A99;">Lost Items - Matches Found:</h3>
                <?php while($row = $result_lost->fetch_assoc()): ?>
                    <div class="match-card">
                        <div class="match-header">
                            ✅ MATCH FOUND! Your lost item has been found!
                        </div>
                        <div class="match-details">
                            <div class="item-section">
                                <h3>Your Lost Item</h3>
                                <p><strong>Item:</strong> <?= htmlspecialchars($row['item_name']) ?></p>
                                <p><strong>Category:</strong> <?= htmlspecialchars($row['category']) ?></p>
                                <p><strong>Lost Date:</strong> <?= htmlspecialchars($row['date_lost']) ?></p>
                                <p><strong>Lost At:</strong> <?= htmlspecialchars($row['place_lost']) ?></p>
                                <p><strong>NFT ID:</strong> <?= htmlspecialchars($row['nft_id']) ?></p>
                                <img src="uploads/lost-items/<?= htmlspecialchars($row['photo1']) ?>" alt="Lost Item">
                            </div>
                            <div class="item-section">
                                <h3>Matched Found Item</h3>
                                <p><strong>Item:</strong> <?= htmlspecialchars($row['found_item_name']) ?></p>
                                <div class="contact-info">
                                    <strong>📞 Contact the finder:</strong><br>
                                    <?= htmlspecialchars($row['found_contact']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
            
            <?php if ($result_found->num_rows > 0): ?>
                <h3 style="color: #004A99;">Found Items - Owner Located:</h3>
                <?php while($row = $result_found->fetch_assoc()): ?>
                    <div class="match-card">
                        <div class="match-header">
                            ✅ MATCH FOUND! The owner of this item has been located!
                        </div>
                        <div class="match-details">
                            <div class="item-section">
                                <h3>Your Found Item</h3>
                                <p><strong>Item:</strong> <?= htmlspecialchars($row['item_name']) ?></p>
                                <p><strong>Category:</strong> <?= htmlspecialchars($row['category']) ?></p>
                                <p><strong>Found Date:</strong> <?= htmlspecialchars($row['date_found']) ?></p>
                                <p><strong>Found At:</strong> <?= htmlspecialchars($row['place_found']) ?></p>
                                <p><strong>NFT ID:</strong> <?= htmlspecialchars($row['nft_id']) ?></p>
                                <img src="uploads/found-items/<?= htmlspecialchars($row['photo1']) ?>" alt="Found Item">
                            </div>
                            <div class="item-section">
                                <h3>Matched Lost Item Owner</h3>
                                <p><strong>Item:</strong> <?= htmlspecialchars($row['lost_item_name']) ?></p>
                                <div class="contact-info">
                                    <strong>📞 Contact the owner:</strong><br>
                                    <?= htmlspecialchars($row['lost_contact']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="no-matches">
                <h3>No matches found yet</h3>
                <p>When your items are matched, they will appear here.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include "footer.php"; ?>
</body>
</html>
