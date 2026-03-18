<?php
$current_page = basename($_SERVER['PHP_SELF']); // get current file name
if (session_status() == PHP_SESSION_NONE) {
    // Session is not started, so start it
    session_start();
}
if (isset($_GET['signout']) && $_GET['signout'] == "1") {
    session_unset();
    session_destroy();
    header("Location: login.html"); // redirect to login or homepage
    exit;
}

?>
<style>
    /* Reset & Layout */
    html,
    body {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

    main {
        flex: 1;
    }

    /* Header */
    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #B3D9F5;
        padding: 10px 30px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1000;
    }

    /* Logo */
    .logo-section {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        background: white;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .site-title {
        font-size: 22px;
        font-weight: bold;
        color: #004A99;
    }

    /* Navbar (Desktop) */
    nav {
        flex-grow: 1;
        display: flex;
        justify-content: center;
        /* keeps items grouped in the center */
        gap: 40px;
        /* adjust spacing between items */
    }

    nav a {
        position: relative;
        text-decoration: none;
        /* no underline always */
        color: #004A99;
        font-weight: 500;
        font-size: 16px;
        padding-bottom: 5px;
    }

    nav a:hover {
        color: #1E64C8;
        text-decoration: none;
        /* ensure no browser underline */
    }

    nav a::after {
        content: "";
        position: absolute;
        width: 0%;
        height: 2px;
        bottom: 0;
        left: 0;
        background: #004A99;
        transition: width 0.3s ease;
    }

    nav a:hover::after {
        width: 100%;
    }

    nav a.active::after {
        width: 100%;
        background: #1E64C8;
    }


    /* Profile container */
    .profile-container {
        position: relative;
        display: flex;
        align-items: center;
        gap: 8px;
        /* small space between name and photo */
        cursor: pointer;
    }

    /* Profile name */
    .profile-name {
        font-weight: bold;
        color: #004A99;
        font-size: 16px;
    }

    /* Profile photo */
    .profile-photo {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #004A99;
    }

    .profile-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Dropdown styling */
    .dropdown {
        position: absolute;
        top: 60px;
        /* below the profile image */
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        width: 180px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .dropdown a {
        padding: 12px 15px;
        text-decoration: none;
        color: #004A99;
        font-size: 14px;
        transition: background 0.2s;
    }

    .dropdown a:hover {
        background: #f0f7ff;
    }

    /* Show dropdown on hover */
    .profile-container:hover .dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    /* Hamburger Button */
    .hamburger {
        display: none;
        flex-direction: column;
        cursor: pointer;
        gap: 5px;
    }

    .hamburger span {
        width: 25px;
        height: 3px;
        background: #004A99;
        border-radius: 3px;
    }

    /* Mobile Navbar */
    @media (max-width: 768px) {
        nav {
            display: block;
            position: absolute;
            top: 60px;
            left: 0;
            background: #B3D9F5;
            width: 100%;
            flex-direction: column;
            align-items: center;
            gap: 10px;

            max-height: 0;
            /* hidden by default */
            overflow: hidden;
            transition: max-height 0.4s ease;
        }

        nav.show {
            max-height: 300px;
            /* expands smoothly */
        }

        nav a {
            display: block;
            padding: 10px 0;
        }

        .hamburger {
            display: flex;
        }
    }
</style>

<header>
    <!-- Left: Logo -->
    <a href="index.php" style="text-decoration:none;">
        <div class="logo-section">
            <div class="logo"><img src="images/logo.png" alt="Logo"></div>
            <div class="site-title">Lost Nest</div>
        </div>
    </a>

    <!-- Navbar -->
    <nav id="navbar">
        <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Home</a>
        <a href="lost-items.php" class="<?= $current_page == 'lost-items.php' ? 'active' : '' ?>">Lost Items</a>
        <a href="found-items.php" class="<?= $current_page == 'found-items.php' ? 'active' : '' ?>">Found Items</a>
        <a href="check-items.php" class="<?= $current_page == 'check-items.php' ? 'active' : '' ?>">Check Items</a>
        <a href="my-matches.php" class="<?= $current_page == 'my-matches.php' ? 'active' : '' ?>">My Matches</a>
        <a href="about.php" class="<?= $current_page == 'about.php' ? 'active' : '' ?>">About Us</a>
    </nav>

    <!-- Hamburger (Mobile) -->
    <div class="hamburger" onclick="toggleMenu()">
        <span></span><span></span><span></span>
    </div>

    <!-- Profile Section -->
    <div class="profile-container">
        <span class="profile-name"><?= $_SESSION["full_name"] ?></span>
        <div class="profile-photo">
            <img src="uploads/<?= $_SESSION["profile_photo"] ?>" alt="Profile">
        </div>

        <!-- Dropdown -->
        <div class="dropdown">
            <a href="edit-profile.php">Edit Profile</a>
            <a href="lost-registry.php">Lost Registry</a>
            <a href="found-registry.php">Found Registry</a>
            <a href="contact.php">Contact Us</a>
            <a href="#" onclick="confirmLogout(event)">Sign Out</a>
        </div>
    </div>
</header>

<script>
    function toggleMenu() {
        document.getElementById("navbar").classList.toggle("show");
    }
</script>
<script>
    function confirmLogout(e) {
    e.preventDefault();
    if (confirm("Are you sure you want to sign out?")) {
        window.location.href = "?signout=1"; // triggers PHP logout above
    }
}
</script>