<?php
include '../includes/auth.php';
include '../database/connection.php';

$driver_id = $_SESSION['id'];
$filter = $_GET['filter'] ?? 'all';

$filename = "timesheet_" . date("Ymd_His") . ".xls";

$whereClause = "WHERE driver_id = $driver_id";

if ($filter == 'today') {
    $start = date('Y-m-d 00:00:00');
    $end = date('Y-m-d 23:59:59');
    $whereClause .= " AND created_at BETWEEN '$start' AND '$end'";
} elseif ($filter == 'week') {
    $start = date('Y-m-d 00:00:00', strtotime('monday this week'));
    $end = date('Y-m-d 23:59:59', strtotime('sunday this week'));
    $whereClause .= " AND created_at BETWEEN '$start' AND '$end'";
} elseif ($filter == 'month') {
    $start = date('Y-m-01 00:00:00');
    $end = date('Y-m-t 23:59:59');
    $whereClause .= " AND created_at BETWEEN '$start' AND '$end'";
} elseif ($filter == 'year') {
    $start = date('Y-01-01 00:00:00');
    $end = date('Y-12-31 23:59:59');
    $whereClause .= " AND created_at BETWEEN '$start' AND '$end'";
}

$query = "SELECT DATE(created_at) AS work_date, 
                 SUM(TIMESTAMPDIFF(MINUTE, time_in, time_out)/60) AS hours_worked 
          FROM employee_time_logs 
          $whereClause 
          GROUP BY DATE(created_at) 
          ORDER BY created_at ASC";

$result = $conn->query($query);

// Set headers for Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Output table header
echo "Date\tHours Worked\n";

// Output table data
$total = 0;
while ($row = $result->fetch_assoc()) {
    $date = $row['work_date'];
    $hours = round($row['hours_worked'], 2);
    echo "$date\t$hours\n";
    $total += $hours;
}

// Output total
$total_hours = floor($total);
$total_minutes = round(($total - $total_hours) * 60);
echo "Total\t{$total_hours} hrs {$total_minutes} mins\n";

$conn->close();
exit;
?>
