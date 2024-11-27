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


// Handle resetting progress
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_progress'])) {
    $reset_sql = "DELETE FROM prayers WHERE user_id = ?";
    $stmt = $conn->prepare($reset_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    $reset_qaza_sql = "UPDATE qaza_prayers SET count = 0 WHERE user_id = ?";
    $stmt = $conn->prepare($reset_qaza_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: qaza_prayers.php");
    exit;
}

// Fetch daily prayer performance for the logged-in user
$sql = "SELECT prayer_name, status, date 
        FROM prayers 
        WHERE user_id = ? AND date = CURDATE()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$daily_report = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $daily_report[] = $row;
    }
}

$stmt->close();

// Fetch total Qaza prayers for the logged-in user
$sql_qaza = "SELECT prayer_name, count 
             FROM qaza_prayers 
             WHERE user_id = ?";
$stmt_qaza = $conn->prepare($sql_qaza);
$stmt_qaza->bind_param("i", $user_id);
$stmt_qaza->execute();
$qaza_result = $stmt_qaza->get_result();

$qaza_prayers = [];
if ($qaza_result->num_rows > 0) {
    while ($row = $qaza_result->fetch_assoc()) {
        $qaza_prayers[] = $row;
    }
}

$stmt_qaza->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Daily Prayer Report</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    
    <style>
        .daily-report-table, .qaza-prayers-table {
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .daily-report-table th, .daily-report-table td,
        .qaza-prayers-table th, .qaza-prayers-table td {
            text-align: center;
            vertical-align: middle;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-missed {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-qaza {
            background-color: #fff3cd;
            color: #856404;
        }

        .no-data {
            text-align: center;
            font-size: 1.2rem;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Daily Prayer Report</h2>
        
        <!-- Daily Report Table -->
        <?php if (!empty($daily_report)): ?>
            <div class="table-responsive daily-report-table">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Prayer</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daily_report as $report): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($report['prayer_name']); ?></td>
                                <td class="<?php echo 'status-' . htmlspecialchars($report['status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($report['status'])); ?>
                                </td>
                                <td><?php echo htmlspecialchars($report['date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>No prayer activity logged for today.</p>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <form method="post">
                <h4>Reset Progress:</h4>
                <button type="submit" name="reset_progress" class="btn btn-danger">Reset All Progress</button>
            </form>
        </div>

        <!-- Total Qaza Prayers Table -->
        <h3 class="mt-5">Total Qaza Prayers</h3>
        <?php if (!empty($qaza_prayers)): ?>
            <div class="table-responsive qaza-prayers-table">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Prayer</th>
                            <th>Missed Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($qaza_prayers as $qaza): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($qaza['prayer_name']); ?></td>
                                <td><?php echo htmlspecialchars($qaza['count']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>You have no Qaza prayers logged.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS & dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
