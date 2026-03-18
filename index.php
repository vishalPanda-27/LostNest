<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost Nest - Home</title>
    <link rel="stylesheet" href="style.css">
    <!-- ✅ Bootstrap for carousel -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #EAF4FB;
        }
        html, body { height: 100%; margin: 0; display: flex; flex-direction: column; }
        main { flex: 1; }
        .main { text-align: center; margin-top: 50px; }
        .tagline { font-size: 20px; font-style: italic; color: #004A99; margin-bottom: 40px; }
        .button-container { display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; }
        .main-btn { padding: 15px 25px; border: 2px solid #004A99; background: white; font-size: 16px; cursor: pointer; border-radius: 8px; transition: 0.3s; color: #004A99; font-weight: bold; text-decoration: none; }
        .main-btn:hover { background: #1E64C8; color: white; border-color: #1E64C8; text-decoration: none;}
        .carousel-item img { height: 250px; object-fit: cover; border-radius: 10px; }
        .carousel-card { text-align: center; }
        .carousel-card h5 { margin-top: 10px; color: #004A99; }
    </style>
</head>

<body>
    <?php include("header.php"); ?>

    <main class="main">
        <p class="tagline">"Register. Search. Reunite."</p>
        <div class="button-container">
            <a href="lost-items.php" class="main-btn">Register Lost Items</a>
            <a href="found-items.php" class="main-btn">Register Found Items</a>
            <a href="check-items.php" class="main-btn">Check Stolen Item Registry</a>
        </div>

        <!-- Lost Items Carousel -->
        <div class="container mt-5">
            <h2 class="mb-3" style="color:#004A99;">Recently Lost Items</h2>
            <div id="lostCarousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    include("config.php");
                    $lostResult = mysqli_query($conn, "SELECT * FROM lost_items ORDER BY created_at DESC LIMIT 6");
                    $active = true;
                    while($row = mysqli_fetch_assoc($lostResult)){
                    $photo1 = $row['photo1'];
                    ?>
                    <div class="carousel-item <?= $active ? 'active' : '' ?>">
                        <div class="carousel-card">
                            <img src="uploads/lost-items/<?=$photo1?>" class="d-block w-100" alt="Lost Item">
                            <h5><?= htmlspecialchars($row['item_name']) ?></h5>
                            <p><?= htmlspecialchars($row['place_lost']) ?></p>
                        </div>
                    </div>
                    <?php $active = false;}?>
                </div>
                <a class="carousel-control-prev" href="#lostCarousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </a>
                <a class="carousel-control-next" href="#lostCarousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </a>
            </div>
        </div>

        <!-- Found Items Carousel -->
        <div class="container mt-5">
            <h2 class="mb-3" style="color:#004A99;">Recently Found Items</h2>
            <div id="foundCarousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    $foundResult = mysqli_query($conn, "SELECT * FROM found_items ORDER BY created_at DESC LIMIT 6");
                    $active = true;
                    while($row = mysqli_fetch_assoc($foundResult)){
                        $photo1 = $row['photo1'];
                    ?>
                    <div class="carousel-item <?= $active ? 'active' : '' ?>">
                        <div class="carousel-card">
                            <img src="uploads/found-items/<?=$photo1?>" class="d-block w-100" alt="Found Item">
                            <h5><?= htmlspecialchars($row['item_name']) ?></h5>
                            <p><?= htmlspecialchars($row['place_found']) ?></p>
                        </div>
                    </div>
                    <?php $active = false;}?>
                </div>
                <a class="carousel-control-prev" href="#foundCarousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </a>
                <a class="carousel-control-next" href="#foundCarousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </a>
            </div>
        </div>

    </main>

    <?php include('footer.php'); ?>
</body>
</html>
