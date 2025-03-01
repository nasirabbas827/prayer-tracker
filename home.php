<?php
include('config.php');

session_start();

// Check if user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

$user_id = $_SESSION["id"];

// Fetch user data (username)
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 1) {
    $stmt->bind_result($username);
    $stmt->fetch();
} else {
    header("location: index.php");
    exit;
}
$stmt->close();

// Handle marking prayers
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['prayer_action'])) {
    $prayer_name = $_POST['prayer_name'];
    $action = $_POST['prayer_action'];

    // Update prayer status in the database
    $sql = "INSERT INTO prayers (user_id, prayer_name, date, status) 
            VALUES (?, ?, CURDATE(), ?) 
            ON DUPLICATE KEY UPDATE status = VALUES(status)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $prayer_name, $action);
    $stmt->execute();
    $stmt->close();

    header("Location: home.php");
    exit;
}

// Fetch today's prayers for the logged-in user
$today_prayers_query = "SELECT prayer_name, status FROM prayers WHERE user_id = ? AND date = CURDATE()";
$stmt = $conn->prepare($today_prayers_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$today_prayers = [];
while ($row = $result->fetch_assoc()) {
    $today_prayers[$row['prayer_name']] = $row['status'];
}
$stmt->close();

// Get prayer counts
$prayer_counts = [
    'completed' => 0,
    'missed' => 0,
    'qaza' => 0,
    'total' => 0
];

$count_query = "SELECT status, COUNT(*) as count FROM prayers WHERE user_id = ? GROUP BY status";
$stmt = $conn->prepare($count_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $prayer_counts[$row['status']] = $row['count'];
    $prayer_counts['total'] += $row['count'];
}
$stmt->close();

// Get time-based greeting
$hour = date('H');
$greeting = '';
if ($hour >= 5 && $hour < 12) {
    $greeting = 'Good Morning';
} elseif ($hour >= 12 && $hour < 18) {
    $greeting = 'Good Afternoon';
} else {
    $greeting = 'Good Evening';
}

// Prayer times (approximate - for display purposes)
$prayer_times = [
    'Fajr' => '05:30 AM',
    'Dhuhr' => '12:30 PM',
    'Asr' => '03:45 PM',
    'Maghrib' => '06:15 PM',
    'Isha' => '07:45 PM'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Prayer Tracker Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #818cf8;
            --primary-dark: #3730a3;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --light: #f9fafb;
            --dark: #1f2937;
            --gray: #6b7280;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            line-height: 1.6;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 16px;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 0;
        }
        
        .dashboard-header h1 {
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }
        
        .dashboard-header p {
            opacity: 0.9;
            margin-bottom: 0;
            font-weight: 300;
            position: relative;
            z-index: 1;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card .stat-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .stat-card .stat-label {
            color: var(--gray);
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .completed-stat .stat-icon {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }
        
        .completed-stat .stat-value {
            color: var(--success);
        }
        
        .missed-stat .stat-icon {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }
        
        .missed-stat .stat-value {
            color: var(--warning);
        }
        
        .qaza-stat .stat-icon {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }
        
        .qaza-stat .stat-value {
            color: var(--danger);
        }
        
        .prayer-cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .prayer-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .prayer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .prayer-card-header {
            padding: 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .prayer-card-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .prayer-time {
            font-size: 0.875rem;
            color: var(--gray);
            display: flex;
            align-items: center;
        }
        
        .prayer-time i {
            margin-right: 0.5rem;
        }
        
        .prayer-card-body {
            padding: 1.5rem;
        }
        
        .prayer-status {
            display: flex;
            align-items: center;
            margin-bottom: 1.25rem;
        }
        
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 0.75rem;
        }
        
        .status-completed .status-indicator {
            background-color: var(--success);
        }
        
        .status-missed .status-indicator {
            background-color: var(--warning);
        }
        
        .status-qaza .status-indicator {
            background-color: var(--danger);
        }
        
        .status-pending .status-indicator {
            background-color: #d1d5db;
        }
        
        .prayer-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-action {
            flex: 1;
            border: none;
            padding: 0.6rem 0;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-completed {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }
        
        .btn-completed:hover {
            background-color: var(--success);
            color: white;
        }
        
        .btn-missed {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }
        
        .btn-missed:hover {
            background-color: var(--warning);
            color: white;
        }
        
        .btn-qaza {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }
        
        .btn-qaza:hover {
            background-color: var(--danger);
            color: white;
        }
        
        .btn-action i {
            margin-right: 0.5rem;
        }
        
        .footer-actions {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }
        
        .btn-view-report {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }
        
        .btn-view-report:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }
        
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 1.5rem;
            }
            
            .prayer-cards-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1><?php echo $greeting; ?>, <?php echo htmlspecialchars($username); ?>!</h1>
            <p>Track your daily prayers and stay consistent in your worship.</p>
        </div>
        
        <!-- Prayer Statistics -->
        <div class="stats-container">
            <div class="stat-card completed-stat">
                <div class="stat-icon">
                    <i class="fas fa-check"></i>
                </div>
                <div class="stat-value"><?php echo $prayer_counts['completed']; ?></div>
                <div class="stat-label">Completed</div>
            </div>
            
            <div class="stat-card missed-stat">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-value"><?php echo $prayer_counts['missed']; ?></div>
                <div class="stat-label">Missed</div>
            </div>
            
            <div class="stat-card qaza-stat">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value"><?php echo $prayer_counts['qaza']; ?></div>
                <div class="stat-label">Qaza</div>
            </div>
        </div>
        
        <!-- Prayer Cards -->
        <h2 class="mb-4">Today's Prayers</h2>
        <div class="prayer-cards-container">
            <?php 
            $prayers = ["Fajr", "Dhuhr", "Asr", "Maghrib", "Isha"];
            foreach ($prayers as $prayer_name): 
                $status = isset($today_prayers[$prayer_name]) ? $today_prayers[$prayer_name] : 'pending';
                $status_text = ucfirst($status);
                if ($status == 'pending') $status_text = 'Not Logged';
            ?>
            <div class="prayer-card">
                <div class="prayer-card-header">
                    <h3><?php echo $prayer_name; ?></h3>
                    <div class="prayer-time">
                        <i class="far fa-clock"></i>
                        <?php echo $prayer_times[$prayer_name]; ?>
                    </div>
                </div>
                <div class="prayer-card-body">
                    <div class="prayer-status status-<?php echo $status; ?>">
                        <div class="status-indicator"></div>
                        <div><?php echo $status_text; ?></div>
                    </div>
                    
                    <form method="post">
                        <input type="hidden" name="prayer_name" value="<?php echo $prayer_name; ?>">
                        <div class="prayer-actions">
                            <button type="submit" name="prayer_action" value="completed" class="btn-action btn-completed">
                                <i class="fas fa-check"></i> Completed
                            </button>
                            <button type="submit" name="prayer_action" value="missed" class="btn-action btn-missed">
                                <i class="fas fa-times"></i> Missed
                            </button>
                            <button type="submit" name="prayer_action" value="qaza" class="btn-action btn-qaza">
                                <i class="fas fa-clock"></i> Qaza
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="footer-actions">
            <a href="qaza_prayers.php" class="btn-view-report">
                <i class="fas fa-chart-bar mr-2"></i> View Detailed Report
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>