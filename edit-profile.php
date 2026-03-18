<?php
include_once "config.php";
 if (session_status() == PHP_SESSION_NONE) {
    // Session is not started, so start it
    session_start();
 }
// Fetch current user details
$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name   = $_POST['full_name'];
    $new_username = $_POST['username'];
    $dob         = $_POST['dob'];
    $password    = $_POST['password'];
    // Handle profile photo
    $profile_photo = $user['profile_photo'] ?? "default.png";
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $profile_photo = $user['username']. "_" .time(). basename($_FILES['profile_photo']['name']);
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_dir . $profile_photo);
    }

    // Build update array
    $data = [
        "full_name"     => $full_name,
        "username"      => $new_username,
        "dob"           => $dob,
        "profile_photo" => $profile_photo
    ];

    if (!empty($password)) {
        $data["password"] = $password; // plain text for now
    }

    // Condition based on old username
    $cond = "username='{$username}'";
    $res=update("users", $data, $cond);
    if ($res=="1") {
        // Update session values
        $_SESSION['username']   = $new_username;
        $_SESSION['full_name']  = $full_name;
        $_SESSION['profile_photo'] = $profile_photo;
        header("Location: index.php?msg=Profile+Updated+Successfully");
        exit;
    } else {
        $error = "❌ Error updating profile.";
    }
}
?>
<?php include "header.php";?>
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: #EAF4FB;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
            color: #004A99;
            margin-bottom: 20px;
        }

        .form-row {
            margin-bottom: 20px;
            /* more space between fields */
        }

        .form-row label {
            display: block;
            font-size: 14px;
            color: #004A99;
            /* Dark Blue */
        }

        input[type="text"],
        input[type="date"],
        input[type="password"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .submit-btn {
            background: #004A99;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background: #1E64C8;
        }

        .profile-preview {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-preview img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 2px solid #004A99;
            object-fit: cover;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Edit Profile</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
<form method="POST" enctype="multipart/form-data">
        <div class="profile-preview">
            <label for="profile_photo" style="cursor: pointer;">
                <img src="uploads/<?=$user['profile_photo']?>"
                    alt="Profile Photo"
                    id="profileImage">
            </label>
            <input type="file" id="profile_photo" name="profile_photo" accept="image/*"
            style="display: none;">
        </div>

        
            <div class="form-row">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name"
                    value="<?= htmlspecialchars($user['full_name']) ?>" required>
            </div>

            <div class="form-row">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                    value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>

            <div class="form-row">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob"
                    value="<?= htmlspecialchars($user['dob']) ?>" required>
            </div>

            <div class="form-row">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                    placeholder="New Password (leave blank to keep current)">
            </div>
            <div class="form-row" style="text-align:center;">
                <button type="submit" class="submit-btn">Update Profile</button>
            </div>
        </form>

    </div>

    <?php include "footer.php"; ?>
</body>
<script>
    document.getElementById("profile_photo").addEventListener("change", function(event) {
        if (event.target.files && event.target.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("profileImage").src = e.target.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    });
</script>

</html>