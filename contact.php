<?php
include "header.php";
include "config.php";

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $message = trim($_POST['message']);

    if ($name && $email && $message) {
        // build a flat associative array
        $data = [
            "name"    => $name,
            "email"   => $email,
            "message" => $message
        ];
        $status= insertInto("contact_messages",$data,true);

        if ($status!=0) {
            $success = "✅ Your message has been sent successfully!";
        } else {
            $error = "❌ Something went wrong. Please try again.";
        }
    } else {
        $error = "⚠️ All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
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
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .form-container h2 {
            text-align: center;
            color: #004A99;
            margin-bottom: 20px;
        }
        .form-row {
            margin-bottom: 15px;
        }
        .form-row label {
            display: block;
            margin-bottom: 5px;
            color: #004A99;
            font-weight: bold;
        }
        .form-row input, .form-row textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        .form-row textarea {
            resize: vertical;
            min-height: 120px;
        }
        .submit-btn {
            background: #004A99;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }
        .submit-btn:hover {
            background: #1E64C8;
        }
        .success { color: green; text-align: center; margin-bottom: 10px; }
        .error { color: red; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Contact Us</h2>
        <?php if (!empty($success)): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-row">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-row">
                <label for="email">Your Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-row">
                <label for="message">Your Message</label>
                <textarea id="message" name="message" required></textarea>
            </div>

            <div class="form-row">
                <button type="submit" class="submit-btn">Send Message</button>
            </div>
        </form>
    </div>

    <?php include "footer.php"; ?>
</body>
</html>
