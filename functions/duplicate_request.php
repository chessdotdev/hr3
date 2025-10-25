<?php


function checkDuplicate($table){
    global $driver_id;
    global $pendingCount;
    global $conn;
    // Check if the driver already has any pending status
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM $table WHERE driver_id = ? AND status = 'Pending'");
    $checkStmt->bind_param("i", $driver_id);
    $checkStmt->execute();
    $checkStmt->bind_result($pendingCount);
    $checkStmt->fetch();
    $checkStmt->close();
}
