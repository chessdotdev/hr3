<?php
include '../includes/header.php';
require '../database/connection.php';

$search_id = isset($_GET['search_id']) ? $_GET['search_id'] : '';

// Base query
$query = "SELECT e.id as employee_id, e.firstname, 
                 l.time_in, l.time_out 
          FROM driver e
          LEFT JOIN (
              SELECT * FROM employee_time_logs
              WHERE DATE(created_at) = CURDATE()
          ) l ON e.id = l.driver_id";

// Add search filter if ID is provided
if(!empty($search_id)){
    $query .= " WHERE e.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $search_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query .= " ORDER BY e.firstname ASC";
    $result = $conn->query($query);
}

$attendance = [];
while($row = $result->fetch_assoc()){
    $attendance[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Attendance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="content">
<div class="container mt-5">
    <h1>Employee Attendance (Today)</h1>

    <!-- Search Form -->
    <form class="mb-3 d-flex gap-2" method="GET">
        <input type="text" name="search_id" class="form-control" placeholder="Search by Employee ID" value="<?php echo htmlspecialchars($search_id); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="attendance.php" class="btn btn-secondary">Reset</a>
    </form>

    <table class="table table-bordered table-striped mt-3">
        <thead class="table-secondary">
            <tr>
                <th>Employee Name</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($attendance) > 0): ?>
                <?php foreach($attendance as $row): ?>
                <tr>
                    <td><?php echo $row['firstname']; ?></td>
                    <td><?php echo $row['time_in'] ?? '-'; ?></td>
                    <td><?php echo $row['time_out'] ?? '-'; ?></td>
                    <td>
                        <?php 
                            if($row['time_in'] && !$row['time_out']){
                                echo "<span class='text-success'>Clocked In</span>";
                            } elseif($row['time_out']){
                                echo "<span class='text-danger'>Clocked Out</span>";
                            } else {
                                echo "<span class='text-muted'>Not Yet Clocked In</span>";
                            }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center">No records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</div>
</body>
</html>


<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>