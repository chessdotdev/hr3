<?php
require '../database/connection.php';

$totalPending = 0;

// Count pending leave requests
$sql1 = "SELECT COUNT(*) FROM leave_request WHERE status = 'Pending'";
$result1 = $conn->query($sql1);
$leave = $result1->fetch_row();
$totalPending += $leave[0]; 

// Count pending shift change requests
$sql2 = "SELECT COUNT(*) FROM shift_req WHERE status = 'Pending'";
$result2 = $conn->query($sql2);
$shift = $result2->fetch_row();
$totalPending += $shift[0];


//claims
$sql3 = "SELECT COUNT(*) FROM claims WHERE status = 'Pending'";
$result3 = $conn->query($sql3);
$claims = $result3->fetch_row();
$totalPending += $claims[0];


// Count pending approvals (e.g., profile updates, registrations, etc.)
$sql4 = "SELECT COUNT(*) FROM schedule  
    WHERE schedule.shift_date IS NULL
        AND schedule.shift_start IS NULL
        AND schedule.shift_end IS NULL
        AND schedule.shift_type IS NULL
        AND schedule.scheduled_at IS NULL
        ";
        
$result4 = $conn->query($sql4);
$schedule = $result4->fetch_row();
print_r($schedule[0]);
$totalPending += $schedule[0];


// Output total count
echo $totalPending;
?>
