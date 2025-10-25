<?php
require '../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shift_id = filter_input(INPUT_POST, 'shift_id', FILTER_VALIDATE_INT);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);

    // Validate input
    if ($shift_id && in_array($action, ['approve', 'reject'])) {
        $status = ($action === 'approve') ? 'Approved' : 'Rejected';

        $stmt = $conn->prepare("UPDATE leave_request SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $shift_id);

        if ($stmt->execute()) {
            header("Location: ../admin/leave.php?success=1");
            exit;
        } else {
            echo "Error updating status: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Invalid input.";
    }

    $conn->close();
} else {
    echo "Invalid request method.";
}
