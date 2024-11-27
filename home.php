<?php
include('config.php');

session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Fetch the user data (username) from the database
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 1) {
    $stmt->bind_result($username);
    $stmt->fetch();
} else {
    // If user data is not found, redirect to login page
    header("location: index.php");
    exit;
}

$stmt->close();



// Handle marking a missed prayer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['missed_prayer'])) {
    $prayer_name = $_POST['prayer_name'];

    // Insert into prayers table as missed
    $insert_sql = "INSERT INTO prayers (user_id, prayer_name, date, status) VALUES (?, ?, CURDATE(), 'missed')";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("is", $user_id, $prayer_name);
    $stmt->execute();
    $stmt->close();

    // Update or insert into qaza_prayers
    $qaza_sql = "INSERT INTO qaza_prayers (user_id, prayer_name, count) 
                 VALUES (?, ?, 1) 
                 ON DUPLICATE KEY UPDATE count = count + 1";
    $stmt = $conn->prepare($qaza_sql);
    $stmt->bind_param("is", $user_id, $prayer_name);
    $stmt->execute();
    $stmt->close();

    header("Location: home.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Home Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    
    <style>
        .dashboard-welcome {
            background-color: #f8f9fa;
            padding: 30px;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dashboard-welcome h2 {
            font-size: 2rem;
            color: #007bff;
        }

        .dashboard-welcome p {
            font-size: 1.2rem;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <div class="dashboard-welcome">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
            <p>This is your dashboard. From here, you can manage your account settings, view your details, and more.</p>
        </div>



        <div class="mt-4">
            <form method="post">
                <h4>Mark Missed Prayer:</h4>
                <select name="prayer_name" class="form-control mb-2">
                    <option value="Fajr">Fajr</option>
                    <option value="Dhuhr">Dhuhr</option>
                    <option value="Asr">Asr</option>
                    <option value="Maghrib">Maghrib</option>
                    <option value="Isha">Isha</option>
                </select>
                <button type="submit" name="missed_prayer" class="btn btn-warning">Mark as Missed</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS & dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
