<?php
session_start();
require '../database/connection.php';

$employee_id = $_SESSION['id'];

// Fetch today's log
$query = "SELECT * FROM employee_time_logs 
          WHERE driver_id = ? 
          AND DATE(created_at) = CURDATE() 
          ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$log = $result->fetch_assoc();

if ($log) {
    if (!is_null($log['time_out'])) {
        echo "offline"; 
    } else {
        echo "Go online"; 
    }
} else {
    echo "Go online";
}
?>
