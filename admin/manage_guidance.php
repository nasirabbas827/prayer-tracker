<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Handle form submission to add a new guidance entry
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_guidance"])) {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $video_url = !empty($_POST["video_url"]) ? trim($_POST["video_url"]) : null;

    if (!empty($title)) {
        $sql = "INSERT INTO prayer_guidance (title, description, video_url) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $title, $description, $video_url);
        $stmt->execute();
        $stmt->close();
        $success_message = "Prayer guidance added successfully!";
    } else {
        $error_message = "Title is required.";
    }
}

// Handle deletion of a guidance entry
if (isset($_GET["delete_id"])) {
    $delete_id = intval($_GET["delete_id"]);
    $sql_delete = "DELETE FROM prayer_guidance WHERE guidance_id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $success_message = "Prayer guidance deleted successfully!";
    } else {
        $error_message = "Failed to delete guidance. Please try again.";
    }
    $stmt->close();
}

// Fetch all prayer guidance entries
$sql_guidance = "SELECT * FROM prayer_guidance ORDER BY created_at DESC";
$result_guidance = $conn->query($sql_guidance);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Prayer Guidance</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .form-section, .guidance-table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">

        <h1>Manage Prayer Guidance</h1>

        <!-- Success/Error Message -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Add Prayer Guidance Form -->
        <div class="form-section">
            <h2>Add New Prayer Guidance</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                </div>
                <div class="form-group">
                    <label for="video_url">Video URL (Optional)</label>
                    <input type="url" class="form-control" id="video_url" name="video_url">
                </div>
                <button type="submit" class="btn btn-primary" name="add_guidance">Add Guidance</button>
            </form>
        </div>
        </div>
        </div>

        <!-- Display All Prayer Guidance -->
        <div class="guidance-table">
            <h2>Existing Prayer Guidance</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Video URL</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_guidance->num_rows > 0): ?>
                            <?php while ($row = $result_guidance->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row["guidance_id"]; ?></td>
                                    <td><?php echo htmlspecialchars($row["title"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["description"]); ?></td>
                                    <td>
                                        <?php if (!empty($row["video_url"])): ?>
                                            <a href="<?php echo htmlspecialchars($row["video_url"]); ?>" target="_blank">View</a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $row["created_at"]; ?></td>
                                    <td><?php echo $row["updated_at"]; ?></td>
                                    <td>
                                        <a href="edit_guidance.php?id=<?php echo $row['guidance_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="?delete_id=<?php echo $row['guidance_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this guidance?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No guidance entries found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS & dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
