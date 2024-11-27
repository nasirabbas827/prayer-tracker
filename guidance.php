<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

// Fetch prayer guidance from the database
$sql_guidance = "SELECT * FROM prayer_guidance ORDER BY created_at DESC";
$result_guidance = $conn->query($sql_guidance);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Prayer Guidance</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .section-title {
            margin-top: 40px;
            text-align: center;
        }

        .card-deck .card {
            margin-bottom: 30px;
        }

        .prayer-section {
            margin-top: 60px;
        }
    </style>
</head>
<body>

<?php
include('navbar.php');
?>

<!-- Prayer Guidance Section -->
<div class="container prayer-section">
    <h2 class="section-title">Prayer Guidance</h2>
    <?php if ($result_guidance->num_rows > 0): ?>
        <div class="card-deck">
            <?php while ($row = $result_guidance->fetch_assoc()): ?>
                <div class="card">
                    <img src="images/prayer.jpg" class="card-img-top" alt="<?php echo htmlspecialchars($row["title"]); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row["title"]); ?></h5>
                        <p class="card-text"><?php echo substr(htmlspecialchars($row["description"]), 0, 100); ?>...</p>
                        <a href="<?php echo htmlspecialchars($row["video_url"]); ?>" target="_blank" class="btn btn-primary">View Video</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No prayer guidance available at the moment.</p>
    <?php endif; ?>
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
