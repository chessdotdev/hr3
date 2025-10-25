<?php
// Include your DB connection and auth
include '../includes/auth.php';
require '../database/connection.php';
include '../includes/loader.php';
$driver_id = $_SESSION["id"]; // From session

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shift_date = $_POST['shift_date'];
    $shift_start = $_POST['shift_start'];
    $shift_end = $_POST['shift_end'];
     
    include '../includes/timeFormat.php';

    $stmt = $conn->prepare("INSERT INTO schedule (shift_date, shift_start, shift_end, shift_type, driver_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi",  $shift_date, $start, $end, $shift_type, $driver_id);
    

    if ($stmt->execute()) {
        $success = "Schedule inserted successfully!";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Schedule</title>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Add Driver Schedule</h4>
        </div>
        <div class="card-body">

            <!-- Success / Error Message -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php elseif (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="shift_date" class="form-label">Shift Date</label>
                    <input type="date" name="shift_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="shift_start" class="form-label">Start Time</label>
                    <input type="time" name="shift_start" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="shift_end" class="form-label">End Time</label>
                    <input type="time" name="shift_end" class="form-control" required>
                </div>

                

                <button type="submit" class="btn btn-success">Add Schedule</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
