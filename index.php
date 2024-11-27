<!DOCTYPE html>
<html lang="en">
<head>
    <title>Prayer Tracker</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .jumbotron {
            height: 500px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./images/prayer.jpg');
            background-size: cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .jumbotron h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .jumbotron p {
            font-size: 1.5rem;
        }

        .section-title {
            margin-top: 40px;
            text-align: center;
        }

        .card{
            margin-bottom:20px;
        }

        .card-deck .card {
            margin-bottom: 30px;
        }

        .prayer-section {
            margin-top: 60px;
        }

        .footer {
            margin-top: 50px;
            padding: 20px 0;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

<?php
include('navbar.php');
?>

<!-- Jumbotron (Hero Section) -->
<div class="jumbotron text-center">
    <h1>Welcome to Prayer Tracker</h1>
    <p>Track your daily prayers and stay on top of your spiritual commitments.</p>
    <a href="login.php" class="btn btn-primary btn-lg">Login to Get Started</a>
</div>

<!-- About the Project Section -->
<div class="container prayer-section">
    <h2 class="section-title">About Prayer Tracker</h2>
    <p class="lead text-center">
        Prayer Tracker is a web application designed to help you keep track of your five daily prayers. It allows you to log missed prayers (Qaza) and provides guidance to improve your prayer performance.
    </p>
</div>

<!-- Five Daily Prayers Section -->
<div class="container prayer-section">
    <h2 class="section-title">The Five Daily Prayers</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <img src="./images/prayer.jpg" class="card-img-top" alt="Fajr">
                <div class="card-body">
                    <h5 class="card-title">Fajr</h5>
                    <p class="card-text">Fajr is the first of the five daily prayers, performed before dawn. It consists of two rakats and is performed before sunrise.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <img src="./images/prayer.jpg" class="card-img-top" alt="Zuhr">
                <div class="card-body">
                    <h5 class="card-title">Zuhr</h5>
                    <p class="card-text">Zuhr is the second prayer of the day, offered after midday when the sun has passed its zenith. It consists of four rakats.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <img src="./images/prayer.jpg" class="card-img-top" alt="Asr">
                <div class="card-body">
                    <h5 class="card-title">Asr</h5>
                    <p class="card-text">Asr is the third prayer, offered in the afternoon. It consists of four rakats and is performed before sunset.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <img src="./images/prayer.jpg" class="card-img-top" alt="Maghrib">
                <div class="card-body">
                    <h5 class="card-title">Maghrib</h5>
                    <p class="card-text">Maghrib is the fourth prayer, offered just after sunset. It consists of three rakats and is a time of reflection after the day's work.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <img src="./images/prayer.jpg" class="card-img-top" alt="Isha">
                <div class="card-body">
                    <h5 class="card-title">Isha</h5>
                    <p class="card-text">Isha is the fifth and final prayer of the day, performed at night. It consists of four rakats and is the last prayer before going to sleep.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Prayer Guidance Section -->
<div class="container prayer-section">
    <h2 class="section-title">Prayer Guidance</h2>
    <div class="card-deck">
        <!-- Loop through prayer guidance and display each one as a card -->
        <?php
        include "config.php";
        $sql_guidance = "SELECT * FROM prayer_guidance ORDER BY created_at DESC LIMIT 3";
        $result_guidance = $conn->query($sql_guidance);

        if ($result_guidance->num_rows > 0) {
            while ($row = $result_guidance->fetch_assoc()) {
                echo '
                <div class="card">
                    <img src="images/prayer.jpg" class="card-img-top" alt="' . htmlspecialchars($row["title"]) . '">
                    <div class="card-body">
                        <h5 class="card-title">' . htmlspecialchars($row["title"]) . '</h5>
                        <p class="card-text">' . substr(htmlspecialchars($row["description"]), 0, 100) . '...</p>
                        <a href="' . htmlspecialchars($row["video_url"]) . '" target="_blank" class="btn btn-primary">View Video</a>
                    </div>
                </div>';
            }
        } else {
            echo '<p>No prayer guidance available.</p>';
        }
        ?>
    </div>
</div>

<!-- Footer Section -->
<footer class="footer">
    <div class="container text-center">
        <p>&copy; 2024 Prayer Tracker. All rights reserved.</p>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
