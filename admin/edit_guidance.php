<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch the guidance data for editing
if (isset($_GET["id"])) {
    $guidance_id = intval($_GET["id"]);
    $sql = "SELECT * FROM prayer_guidance WHERE guidance_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $guidance_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $guidance = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission to update guidance
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_guidance"])) {
    $guidance_id = intval($_POST["guidance_id"]);
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $video_url = !empty($_POST["video_url"]) ? trim($_POST["video_url"]) : null;

    if (!empty($title)) {
        $sql_update = "UPDATE prayer_guidance SET title = ?, description = ?, video_url = ? WHERE guidance_id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("sssi", $title, $description, $video_url, $guidance_id);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_guidance.php");
        exit;
    } else {
        $error_message = "Title is required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Prayer Guidance</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">

        <h1>Edit Prayer Guidance</h1>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="guidance_id" value="<?php echo $guidance["guidance_id"]; ?>">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($guidance["title"]); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($guidance["description"]); ?></textarea>
            </div>
            <div class="form-group">
                <label for="video_url">Video URL (Optional)</label>
                <input type="url" class="form-control" id="video_url" name="video_url" value="<?php echo htmlspecialchars($guidance["video_url"]); ?>">
            </div>
            <button type="submit" class="btn btn-success" name="update_guidance">Update Guidance</button>
        </form>
    </div>
    </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
