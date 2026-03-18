<?php
include "config.php";
include "header.php";

// Fetch lost items
$sql = "SELECT * FROM lost_items ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lost Items Registry</title>
    <link rel="stylesheet" href="style.css">
    <style>
        html,body {
            background: #EAF4FB;
            font-family: Arial, sans-serif;
            height:100%;
            margin: 0;
            padding: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            background: #EAF4FB;
            font-family: Arial, sans-serif;
        }
        .content {
            flex: 1; /* take remaining height */
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 10px;
        }
        h2 {
            color: #004A99;
            margin-bottom: 20px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .card img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 2px solid #004A99;
        }
        .card h3 {
            margin: 5px 0;
            color: #004A99;
        }
        .card p {
            margin: 3px 0;
            font-size: 14px;
        }
        .empty {
            text-align: center;
            color: #666;
            font-size: 16px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Lost Items List:</h2>
        <div class="grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                        // pick first available photo
                        $photo = $row['photo1'] ?: ($row['photo2'] ?: ($row['photo3'] ?: 'default.png'));
                    ?>
                    <div class="card">
                        <img src="uploads/lost-items/<?= htmlspecialchars($photo) ?>" alt="Lost Item">
                        <h3><?= htmlspecialchars($row['item_name']) ?></h3>
                        <p><strong>Category:</strong> <?= htmlspecialchars($row['category']) ?></p>
                        <p><strong>Lost Date:</strong> <?= htmlspecialchars($row['date_lost']) ?></p>
                        <p><strong>Lost At:</strong> <?= htmlspecialchars($row['place_lost']) ?></p>
                        <p><strong>Contact:</strong> <?= htmlspecialchars($row['contact_number']) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty">No lost items have been reported yet.</div>
            <?php endif; ?>
        </div>
    </div>

    <?php include "footer.php"; ?>
</body>
</html>
