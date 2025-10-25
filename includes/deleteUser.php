<?php
require '../database/connection.php';
$deleteMessageSuccess = '';
if (isset($_POST['delete_user']) && isset($_POST['delete_user_id'])) {
    $user_id = intval($_POST['delete_user_id']);

    // Optional: prevent deletion of currently logged-in user (for safety)
    // if ($_SESSION['id'] == $user_id) {
    //     echo "<div class='alert alert-warning'>You cannot delete your own account.</div>";
    // } else {

    // Perform deletion
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $deleteMessageSuccess = "<div class='alert alert-success'>User deleted successfully.</div>";
    } else {
        // echo "<div class='alert alert-danger'>Error deleting user: " . $stmt->error . "</div>";
    }

    $stmt->close();
    // }
    header('location: ../admin/users.php');
        exit();
}
?>