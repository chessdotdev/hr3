<?php
require '../database/connection.php';
header('Content-Type: application/json');

// Initialize counts
$data = [
    'Approved' => 0,
    'Rejected' => 0,
    'Pending' => 0
];

$sql = "SELECT status, COUNT(*) AS count FROM claims GROUP BY status";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $status = $row['status'];
    if (isset($data[$status])) {
        $data[$status] = (int) $row['count'];
    }
}

echo json_encode($data);
