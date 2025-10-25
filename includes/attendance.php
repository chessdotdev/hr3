<?php
session_start();
require '../database/connection.php';

$employee_id = $_SESSION['id']; 

$timoutMessage  = '';
$message = '';
// Check if employee already timed in today
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
    // If already timed in, this is a Time Out
    if (is_null($log['time_out'])) {
        $update = "UPDATE employee_time_logs SET time_out = NOW() WHERE id = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("i", $log['id']);
        $stmt->execute();
       header('location:../employee/home.php');
       echo  "<script>alert('You have timed out successfully!');</script>";

    } else {
        $message = "You have already time out today.";
        header('location:../employee/home.php');

    }
} else {
    // Time In
    $insert = "INSERT INTO employee_time_logs (driver_id, time_in) VALUES (?, NOW())";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    echo "You have timed in successfully!";
    header('location:../employee/home.php');

}
?>
