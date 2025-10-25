<?php
require '../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shift_id = intval($_POST['shift_id']);
    $action = $_POST['action']; // approve or reject
    if (!$shift_id || !in_array($action, ['approve', 'reject'])) {
            header("Location: ../admin/shift_requests.php?error=Invalid request");
            exit;
        }

    // Fetch shift details
    $stmt = $conn->prepare("SELECT * FROM shift_req WHERE shift_id = ?");
    $stmt->bind_param("i", $shift_id);
    $stmt->execute();
    $shift = $stmt->get_result()->fetch_assoc();

    if (!$shift) {
        header("Location: ../admin/shift_requests.php?error=Shift not found");
        exit;
    }

    // Update status in shift_req
    $status = ($action === 'approve') ? 'APPROVED' : 'REJECTED';
    $stmt = $conn->prepare("UPDATE shift_req SET status = ? WHERE shift_id = ?");
    $stmt->bind_param("si", $status, $shift_id);
    $stmt->execute();

    // If approved, insert/update into schedule table
    if ($action === 'approve') {
        $driver_id   = $shift['driver_id'];
        $shift_date  = $shift['shift_date'];
        $shift_start = $shift['shift_start'];
        $shift_end   = $shift['shift_end'];
        $shift_type  = $shift['shift_type'];
       var_dump($shift_start);
        // Check if already exists in schedule
        $check = $conn->prepare("SELECT id FROM schedule WHERE driver_id = ? AND shift_date = ?");
        $check->bind_param("ss", $driver_id, $shift_date);
        $check->execute();
        $existing = $check->get_result()->fetch_assoc();

        if ($existing) {
            // Update existing schedule
            $update = $conn->prepare("UPDATE schedule 
                                      SET shift_start=?, shift_end=?, shift_type=? 
                                      WHERE id=?");
            $update->bind_param("sssi", $shift_start, $shift_end, $shift_type, $existing['id']);
            $update->execute();
        } else {
            // Insert new schedule
            $insert = $conn->prepare("INSERT INTO schedule 
                                      (driver_id, shift_date, shift_start, shift_end, shift_type, scheduled_at)
                                      VALUES (?, ?, ?, ?, ?,NOW())");
            $insert->bind_param("sssss", $driver_id, $shift_date, $shift_start, $shift_end, $shift_type);
            $insert->execute();
        }
    }

    header("Location: ../admin/shift_and_schedule.php?success=Shift $status successfully");
    exit;
}
?>
