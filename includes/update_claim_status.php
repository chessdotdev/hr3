<?php
session_start();
require '../database/connection.php';

// Optional: Only allow admin users
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
//     die("Unauthorized access.");
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $claim_id = filter_input(INPUT_POST, 'claim_id', FILTER_VALIDATE_INT);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    if (!$claim_id || !in_array($action, ['approve', 'reject'])) {
        die("Invalid input.");
    }

    $new_status = ($action === 'approve') ? 'Approved' : 'Rejected';

    $stmt = $conn->prepare("UPDATE claims SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $new_status, $claim_id);

    if ($stmt->execute()) {
        $_SESSION['flash_message'] = "Claim #$claim_id has been $new_status.";
    } else {
        $_SESSION['flash_message'] = "Failed to update claim: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    $_SESSION['flash_message'] = "Invalid request method.";
}

header("Location: ../admin/reimbursement.php");
exit;
?>
