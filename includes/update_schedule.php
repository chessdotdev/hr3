<?php
require '../database/connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $schedule_id = $_POST['schedule_id'];
    $shift_date  = $_POST['shift_date'];
    $shift_start = $_POST['start_time'];
    $shift_end   = $_POST['end_time'];
    

    include 'timeFormat.php';

    // Update existing schedule row
    $sql = "UPDATE schedule 
            SET shift_date = ?, shift_start = ?, shift_end = ?, shift_type = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $shift_date, $start, $end, $shift_type, $schedule_id);

    if ($stmt->execute()) {
        echo "<script>
                alert('Schedule updated successfully!');
                window.location.href='../admin/schedule.php';
              </script>";
    } else {
        echo "Error updating schedule: " . $stmt->error;
    }
}

