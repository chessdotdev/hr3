<?php
require '../database/connection.php';


function pendingCount($table){
    global $conn;
    $sqlCount = "SELECT COUNT(*) AS pending_count FROM $table WHERE status = 'Pending'"; // try string if needed
    $resultCount = $conn->query($sqlCount);
    
    if ($resultCount) {
        $row = $resultCount->fetch_assoc();
        $pendingCount = $row['pending_count'];
    } else {
        echo "Query failed: " . $conn->error;
        $pendingCount = 0;
    }
    
    echo $pendingCount;
}





?>
