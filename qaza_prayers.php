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

// Handle reset progress action
if (isset($_POST['reset_progress'])) {
    // Delete all qaza prayers for this user
    $delete_qaza = "DELETE FROM prayers WHERE user_id = ?";
    $stmt_delete = $conn->prepare($delete_qaza);
    $stmt_delete->bind_param("i", $user_id);
    $stmt_delete->execute();
    $stmt_delete->close();
    
    // Redirect to refresh the page
    header("Location: " . $_SERVER['PHP_SELF']);
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
$sql_qaza = "SELECT prayer_name, COUNT(*) as count 
             FROM prayers 
             WHERE user_id = ? AND status = 'qaza'
             GROUP BY prayer_name";

$stmt_qaza = $conn->prepare($sql_qaza);
$stmt_qaza->bind_param("i", $user_id);
$stmt_qaza->execute();
$qaza_result = $stmt_qaza->get_result();

$qaza_prayers = [];
if ($qaza_result->num_rows > 0) {
    while ($row = $qaza_result->fetch_assoc()) {
        $qaza_prayers[$row['prayer_name']] = $row['count'];
    }
}

$stmt_qaza->close();

// Count prayers by status
$prayer_counts = [
    'completed' => 0,
    'missed' => 0,
    'qaza' => 0
];

foreach ($daily_report as $prayer) {
    $prayer_counts[$prayer['status']]++;
}

// Total qaza count
$total_qaza = array_sum($qaza_prayers);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Prayer Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: none;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }
        
        .prayer-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            flex: 1;
            min-width: 200px;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        
        .stat-card h3 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        
        .stat-card p {
            margin: 0;
            color: #6c757d;
            font-weight: 500;
        }
        
        .completed-card {
            background-color: #e3f7e9;
            color: #0d6832;
        }
        
        .missed-card {
            background-color: #fbe7e9;
            color: #a02a37;
        }
        
        .qaza-card {
            background-color: #fff8e1;
            color: #b78105;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            font-weight: 600;
            border-top: none;
        }
        
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .status-completed {
            background-color: #e3f7e9;
            color: #0d6832;
        }
        
        .status-missed {
            background-color: #fbe7e9;
            color: #a02a37;
        }
        
        .status-qaza {
            background-color: #fff8e1;
            color: #b78105;
        }
        
        .chart-container {
            height: 300px;
            position: relative;
        }
        
        .btn-reset {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-reset:hover {
            background-color: #c82333;
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }
        
        .no-data {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .no-data p {
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .prayer-stats {
                flex-direction: column;
            }
            
            .stat-card {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="dashboard-container mt-4">
        <h2 class="mb-4">Prayer Dashboard</h2>
        
        <!-- Prayer Statistics -->
        <div class="prayer-stats">
            <div class="stat-card completed-card">
                <h3><?php echo $prayer_counts['completed']; ?></h3>
                <p>Completed</p>
            </div>
            <div class="stat-card missed-card">
                <h3><?php echo $prayer_counts['missed']; ?></h3>
                <p>Missed</p>
            </div>
            <div class="stat-card qaza-card">
                <h3><?php echo $prayer_counts['qaza']; ?></h3>
                <p>Qaza Today</p>
            </div>
            <div class="stat-card qaza-card" style="background-color: #fff3cd;">
                <h3><?php echo $total_qaza; ?></h3>
                <p>Total Qaza</p>
            </div>
        </div>
        
        <div class="row">
            <!-- Daily Report Table -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        Today's Prayer Report
                    </div>
                    <div class="card-body">
                        <?php if (!empty($daily_report)): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
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
                                                <td>
                                                    <span class="status-badge status-<?php echo htmlspecialchars($report['status']); ?>">
                                                        <?php echo ucfirst(htmlspecialchars($report['status'])); ?>
                                                    </span>
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
                                <a href="home.php" class="btn btn-primary">Log Prayer</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Qaza Prayers Table -->
                <div class="card mt-4">
                    <div class="card-header">
                        Qaza Prayers
                    </div>
                    <div class="card-body">
                        <?php if (!empty($qaza_prayers)): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Prayer</th>
                                            <th>Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($qaza_prayers as $prayer => $count): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($prayer); ?></td>
                                                <td><?php echo htmlspecialchars($count); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="no-data">
                                <p>No qaza prayers recorded.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Prayer Status Chart -->
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        Prayer Status Distribution
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="prayerChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Reset Progress -->
                <div class="card mt-4">
                    <div class="card-header">
                        Reset Progress
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">This will delete all your qaza prayers. This action cannot be undone.</p>
                        <form method="post" onsubmit="return confirm('Are you sure you want to reset all your qaza prayers? This action cannot be undone.');">
                            <button type="submit" name="reset_progress" class="btn-reset">Reset All Progress</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS & dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        // Initialize the prayer status chart
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('prayerChart').getContext('2d');
            
            const chartData = {
                labels: ['Completed', 'Missed', 'Qaza'],
                datasets: [{
                    data: [
                        <?php echo $prayer_counts['completed']; ?>,
                        <?php echo $prayer_counts['missed']; ?>,
                        <?php echo $prayer_counts['qaza']; ?>
                    ],
                    backgroundColor: [
                        '#0d6832',
                        '#a02a37',
                        '#b78105'
                    ],
                    borderColor: [
                        '#e3f7e9',
                        '#fbe7e9',
                        '#fff8e1'
                    ],
                    borderWidth: 2
                }]
            };
            
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 12,
                                family: "'Poppins', sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            };
            
            new Chart(ctx, {
                type: 'pie',
                data: chartData,
                options: chartOptions
            });
        });
    </script>
</body>

</html>