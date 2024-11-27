<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch total number of users
$sql_users = "SELECT COUNT(*) as total_users FROM users";
$result_users = $conn->query($sql_users);
$total_users = $result_users->fetch_assoc()['total_users'];

// Fetch total number of missed prayers
$sql_missed = "SELECT COUNT(*) as total_missed FROM prayers WHERE status = 'missed'";
$result_missed = $conn->query($sql_missed);
$total_missed_prayers = $result_missed->fetch_assoc()['total_missed'];

// Fetch total number of Qaza prayers
$sql_qaza = "SELECT SUM(count) as total_qaza FROM qaza_prayers";
$result_qaza = $conn->query($sql_qaza);
$total_qaza_prayers = $result_qaza->fetch_assoc()['total_qaza'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    
    <style>
        .dashboard-metric {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .dashboard-metric h2 {
            font-size: 2rem;
            color: #007bff;
        }

        .dashboard-metric p {
            font-size: 1.2rem;
            margin-top: 10px;
        }

        .dashboard-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }
    </style>
</head>
<body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-5">
        <h1>Admin Dashboard</h1>
        <div class="dashboard-row">
            <div class="col-lg-4 col-md-6 dashboard-metric">
                <h2><?php echo $total_users; ?></h2>
                <p>Total Users</p>
            </div>
            <div class="col-lg-4 col-md-6 dashboard-metric">
                <h2><?php echo $total_missed_prayers ?? 0; ?></h2>
                <p>Total Missed Prayers</p>
            </div>
            <div class="col-lg-4 col-md-6 dashboard-metric">
                <h2><?php echo $total_qaza_prayers ?? 0; ?></h2>
                <p>Total Qaza Prayers</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS & dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
