<?php
include '../includes/auth.php';
include '../database/connection.php';
include '../includes/loader.php';
include '../functions/duplicate_request.php';

$message = '';
$reasonErr = '';
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $driver_id = $_SESSION['id'];
        $reason = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_SPECIAL_CHARS);
        $start_date = filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_SPECIAL_CHARS);
        $end_date = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_SPECIAL_CHARS);
        $status = 'Pending';

        $allowed_ext = array('jpg', 'png', 'jpeg');
        $file_name = $_FILES['upload']['name'] ?? '';
        $file_size = $_FILES['upload']['size'] ?? 0;
        $file_tmp = $_FILES['upload']['tmp_name'] ?? '';
        $target_dir = '';

        // Validation
        if (!empty($file_name)) {
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $maxSize = 10485760; // 10MB

            if (!in_array($file_ext, $allowed_ext)) {
                $message = "<p class='text-danger'>Invalid file type. Allowed types: jpg, png, jpeg.</p>";
            } elseif ($file_size > $maxSize) {
                $message = "<p class='text-danger'>File is too large.</p>";
            } else {
                // Valid file, move it
                $target_dir = "../Uploads/" . basename($file_name);
                if (!move_uploaded_file($file_tmp, $target_dir)) {
                    $message = "<p class='text-danger'>Failed to upload file.</p>";
                }
            }
        }
        checkDuplicate('leave_request');
        if ($pendingCount > 0) {
            $message = "<p class='text-warning'>You already have a pending leave request. Please wait for it to be reviewed.</p>";
        }
        if (empty($message)) {
            if (empty($end_date) && empty($target_dir)) {
                $sql = "INSERT INTO leave_request (reason, start_date, created_at, status, driver_id) VALUES (?, ?, NOW(), ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sssi', $reason, $start_date, $status, $driver_id);
            } elseif (empty($end_date)) {
                $sql = "INSERT INTO leave_request (reason, start_date, documents, created_at, status, driver_id) VALUES (?, ?, ?, NOW(), ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssssi', $reason, $start_date, $target_dir, $status, $driver_id);
            } elseif (empty($target_dir)) {
                $sql = "INSERT INTO leave_request (reason, start_date, end_date, created_at, status, driver_id) VALUES (?, ?, ?, NOW(), ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssssi', $reason, $start_date, $end_date, $status, $driver_id);
            } else {
                $sql = "INSERT INTO leave_request (reason, start_date, end_date, documents, created_at, status, driver_id) VALUES (?, ?, ?, ?, NOW(), ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sssssi', $reason, $start_date, $end_date, $target_dir, $status, $driver_id);
            }

            if ($stmt->execute()) {
                $message = "<p class='text-success'>Leave request submitted successfully.</p>";
            } else {
                $message = "<p class='text-danger'>Error submitting request: " . $stmt->error . "</p>";
            }
        }
    }

    // --- Fetch Leave Requests ---
    $driver_id = $_SESSION['id'];
    $filter = $_GET['filter'] ?? 'all';

    $whereClause = "WHERE driver_id = ?";
    switch ($filter) {
        case 'week':
            $whereClause .= " AND WEEK(created_at) = WEEK(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
            break;
        case 'month':
            $whereClause .= " AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
            break;
        case 'year':
            $whereClause .= " AND YEAR(created_at) = YEAR(CURDATE())";
            break;
    }

    $sql = "SELECT reason, start_date, end_date, documents, created_at, status 
            FROM leave_request 
            $whereClause 
            ORDER BY created_at DESC";

    $fetchStmt = $conn->prepare($sql);
    $fetchStmt->bind_param("i", $driver_id);
    $fetchStmt->execute();
    $requests = $fetchStmt->get_result();

} catch (Exception $e) {
    $message = "<p class='text-danger'>An unexpected error occurred: " . $e->getMessage() . "</p>";
} finally {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            background: #1f2937;
            color: #ffffff;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .container {
            max-width: 900px;
            padding: 20px;
        }

        .navbar {
            background: rgba(17, 24, 39, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            max-width: 900px;
            margin: 20px auto;
            padding: 10px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 20px;
            z-index: 1000;
        }

        .nav-item a {
            color: #d1d5db;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-item a:hover {
            background: #3b82f6;
            color: #ffffff;
        }

        .card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(6px);
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #4b5563;
            color: #ffffff;
            border-radius: 8px;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }

        .form-check-input {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid #4b5563;
        }

        .form-check-input:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .btn-primary {
            background: #3b82f6;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #1e40af;
        }

        .btn-success {
            background: #22c55e;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background: #16a34a;
        }

        .btn-outline-light {
            border-radius: 8px;
            padding: 8px 16px;
        }

        .btn-outline-light:hover {
            background: #3b82f6;
            color: #ffffff;
            border-color: #3b82f6;
        }

        .alert {
            background: rgba(255, 255, 255, 0.1);
            color: #d1d5db;
            border: none;
            border-radius: 8px;
        }

        .italic-size {
            font-size: 0.9rem;
        }

        .status-pending {
            color: #f59e0b;
            font-weight: 600;
        }

        .status-approved {
            color: #22c55e;
            font-weight: 600;
        }

        .status-rejected {
            color: #dc2626;
            font-weight: 600;
        }

        .table {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            color: #d1d5db;
        }

        .table thead {
            background: rgba(255, 255, 255, 0.1);
        }

        .table th,
        .table td {
            border: none;
            padding: 12px;
        }

        .dropdown-menu {
            background: rgba(17, 24, 39, 0.95);
            border: none;
            border-radius: 8px;
        }

        .dropdown-item {
            color: #d1d5db;
        }

        .dropdown-item:hover {
            background: #3b82f6;
            color: #ffffff;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-wrap: wrap;
                padding: 10px;
            }

            .nav-item a {
                font-size: 0.85rem;
                padding: 6px 12px;
            }

            .card {
                padding: 16px;
            }
        }

        @media (max-width: 475px) {
            .nav-item a {
                font-size: 0.8rem;
                padding: 6px 10px;
            }

            .card-title {
                font-size: 1.25rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-item"><a href="../employee/home.php">Dashboard</a></div>
        <div class="nav-item"><a href="shift.php">Shift Request</a></div>
        <div class="nav-item"><a href="leave.php">Leave Request</a></div>
        <div class="nav-item"><a href="schedule.php">Schedule</a></div>
    </nav>

    <div class="container my-5">
        <div class="card">
            <h2 class="card-title">Leave Request</h2>
            <div class="alert">
                <?= isset($message) ? $message : '' ?>
            </div>

            <form action="leave.php" method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                <div class="mb-3">
                    <label for="reason" class="form-label">Reason for Leave</label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="e.g., Sick leave (see medical certificate)" required></textarea>
                    <div class="invalid-feedback">Please provide a reason for your leave.</div>
                </div>

                <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" required min="<?php echo date('Y-m-d'); ?>">
                    <div class="invalid-feedback">Please select a valid start date (today or later).</div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="until_cleared" name="until_cleared">
                    <label class="form-check-label" for="until_cleared">
                        (no specific end date)
                    </label>
                </div>

                <div class="mb-3" id="endDateContainer">
                    <label for="end_date" class="form-label">End Date (if known)</label>
                    <input type="date" class="form-control" id="end_date" name="end_date">
                    <div class="invalid-feedback">Please select a valid end date.</div>
                </div>

                <div class="mb-3">
                    <label for="file_upload" class="form-label"> (Optional): Documents <span><i class="italic-size">note: maximum 10MB</i></span></label>
                    <input type="file" class="form-control" id="file_upload" name="upload">
                    <div class="invalid-feedback">Please select a valid file.</div>
                </div>

                <div class="d-flex flex-column gap-2 mt-4">
                    <div class="col-12 col-md-12 mb-2 mb-md-0">
                        <button type="submit" class="btn btn-primary w-100">Submit Request</button>
                    </div>
                    <div class="col-12 col-md-12 mt-2">
                        <a href="../employee/home.php" class="btn btn-outline-light w-100">Back to Dashboard</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card mt-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="card-title mb-0">Status</h4>
                <div class="d-flex align-items-center">
                    <form method="get" class="me-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo ($filter == 'all') ? 'Filter by Date' : ucfirst($filter); ?>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                <li><button class="dropdown-item" type="submit" name="filter" value="all">All</button></li>
                                <li><button class="dropdown-item" type="submit" name="filter" value="week">This Week</button></li>
                                <li><button class="dropdown-item" type="submit" name="filter" value="month">This Month</button></li>
                                <li><button class="dropdown-item" type="submit" name="filter" value="year">This Year</button></li>
                            </ul>
                        </div>
                    </form>
                    <a href="schedule.php" class="btn btn-success">View Schedule</a>
                </div>
            </div>

            <?php if ($requests->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date Requested</th>
                                <th>Reason</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Documents</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $requests->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    <td><?php echo htmlspecialchars($row['reason']); ?></td>
                                    <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['end_date'] ?: 'N/A'); ?></td>
                                    <td>
                                        <?php if ($row['documents']): ?>
                                            <a href="<?php echo htmlspecialchars($row['documents']); ?>" target="_blank">View</a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td class="status-<?php echo strtolower($row['status']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No leave requests found. Please submit a request to view its status.</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();

        // Disable end date if "Until medically cleared" is checked
        const checkbox = document.getElementById('until_cleared');
        const endDateInput = document.getElementById('end_date');
        checkbox.addEventListener('change', function() {
            endDateInput.disabled = this.checked;
            if (this.checked) endDateInput.value = '';
        });

        // Validate end date is after start date
        document.querySelector('form').addEventListener('submit', function(e) {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            if (!checkbox.checked && endDate && startDate > endDate) {
                e.preventDefault();
                alert('End date must be after start date.');
            }
        });
    </script>
</body>

</html>