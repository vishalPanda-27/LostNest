<?php include("header.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us - Lost Nest</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .about-container {
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .about-container h2 {
            color: #004A99;
            margin-bottom: 20px;
        }
        .about-container p {
            font-size: 16px;
            line-height: 1.6;
            color: #333;
            margin-bottom: 15px;
        }
        .team-section {
            margin-top: 30px;
        }
        .team-section h3 {
            color: #004A99;
            margin-bottom: 20px;
        }
        .team-members {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap; /* allow wrapping */
        }
        .team-members {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* 2 in a row */
    gap: 20px;
}

.member {
    background: #EAF4FB;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.member:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.2);
}

.member img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
}

.member h4 {
    margin: 8px 0 4px;
    color: #004A99;
}

.member p {
    font-size: 14px;
    color: #555;
}

/* ✅ Responsive */
@media (max-width: 900px) {
    .team-members {
        grid-template-columns: repeat(2, 1fr); /* 2x2 */
    }
}

@media (max-width: 500px) {
    .team-members {
        grid-template-columns: 1fr; /* stack */
    }
}

    </style>
</head>
<body>

<div class="about-container">
    <h2>About Lost Nest</h2>
    <p>
        Lost Nest is a community-driven platform designed to help people 
        report and recover lost or found items easily. Our mission is simple: 
        to reunite owners with their belongings through technology.
    </p>
    <p>
        Whether it’s a misplaced wallet, a lost gadget, or an important document, 
        our platform enables you to register items, search for them, and get in touch 
        with those who may have found them.
    </p>

    <div class="team-section">
        <h3>Meet the Team</h3>
        <div class="team-members">
            <div class="member">
                <img src="uploads/profile1.jpg" alt="Team Member">
                <h4>Vishal Panda</h4>
                <a href="https://www.linkedin.com/in/vishal-panda-285026358" style="text-decoration:none; color:blue;">LinkedIn</a>
                <p>Developer</p>
            </div>
            <div class="member">
                <img src="uploads/profile2.jpg" alt="Team Member">
                <h4>Ashim Abhinash Mishra</h4>
                <a href="https://www.linkedin.com/in/ashim-abinash-mishra-384a1b294" style="text-decoration:none; color:blue;">LinkedIn</a>
                <p>UI/UX Designer</p>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>
</body>
</html>
