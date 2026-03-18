<?php 
include "header.php"; 
include "config.php";

// Fetch found items
$sql = "SELECT * FROM found_items ORDER BY created_at DESC"; 
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Found Items Registry</title>
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

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            overflow: hidden;
            padding: 15px;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-4px);
        }

        .card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .card h3 {
            margin: 0;
            color: #004A99;
            font-size: 18px;
        }

        .card p {
            margin: 4px 0;
            font-size: 14px;
        }

        .success {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Found Items List:</h2>
        <div class="grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="card">
                        <img src="uploads/found-items/<?= htmlspecialchars($row['photo1'] ?? 'default.png') ?>" alt="Found Item">
                        <h3><?= htmlspecialchars($row['item_name']) ?></h3>
                        <p><strong>Category:</strong> <?= htmlspecialchars($row['category']) ?></p>
                        <p><strong>Found Date:</strong> <?= htmlspecialchars($row['date_found']) ?></p>
                        <p><strong>Found At:</strong> <?= htmlspecialchars($row['place_found']) ?></p>
                        <p><strong>Contact:</strong> <?= htmlspecialchars($row['contact_number']) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No found items reported yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include "footer.php"; ?>
</body>
</html>
