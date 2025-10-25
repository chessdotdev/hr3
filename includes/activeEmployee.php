<?php
require '../database/connection.php';
        
// Get count of active users
$sqlCount = "SELECT COUNT(*) AS active_count FROM driver WHERE status = 1";
$resultCount = $conn->query($sqlCount);

if ($resultCount) {
    $row = $resultCount->fetch_assoc();
    $activeCount = $row['active_count'];
} else {
    $activeCount = 0;
}

// echo $activeCount;
?>
