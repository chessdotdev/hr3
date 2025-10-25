<?php
include '../includes/auth.php';
include '../database/connection.php';
include '../includes/loader.php';
include '../functions/duplicate_request.php';

$message = '';
$insertSuccess = '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $driver_id = $_SESSION['id']; // Assuming driver_id is stored in session
        $claim_type = filter_input(INPUT_POST, 'claim_type', FILTER_SANITIZE_SPECIAL_CHARS);
        $incident_date = filter_input(INPUT_POST, 'incident_date', FILTER_SANITIZE_SPECIAL_CHARS);
        $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
        $status = 'Pending';

        // File upload handling
        $allowed_ext = ['jpg', 'png', 'jpeg', 'pdf'];
        $file_name = $_FILES['proof']['name'] ?? '';
        $file_size = $_FILES['proof']['size'] ?? 0;
        $file_tmp = $_FILES['proof']['tmp_name'] ?? '';
        $proof_path = null;

        if (!empty($file_name)) {
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $maxSize = 10485760; // 10MB

            if (!in_array($file_ext, $allowed_ext)) {
                $message = "<p class='text-danger'>Invalid file type. Allowed types: jpg, png, jpeg, pdf.</p>";
            } elseif ($file_size > $maxSize) {
                $message = "<p class='text-danger'>File is too large (max 10MB).</p>";
            } else {
                $unique_name = uniqid('proof_', true) . '.' . $file_ext;
                $target_dir = "../Uploads/";
                $proof_path = $target_dir . $unique_name;
                if (!move_uploaded_file($file_tmp, $proof_path)) {
                    $message = "<p class='text-danger'>Failed to upload file.</p>";
                }
            }
        }

        // Check for duplicate pending claims
        checkDuplicate('claims');
        if (!empty($pendingCount) && $pendingCount > 0) {
            $message = "<p class='text-warning'>You already have a pending claim. Please wait for it to be processed.</p>";
        }

        // Insert into database
        if (empty($message)) {
            if (empty($proof_path)) {
                $sql = "INSERT INTO claims (driver_id, claim_type, incident_date, amount, description, created_at, status)
                        VALUES (?, ?, ?, ?, ?, NOW(), ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('issdss', $driver_id, $claim_type, $incident_date, $amount, $description, $status);
            } else {
                $sql = "INSERT INTO claims (driver_id, claim_type, incident_date, amount, description, proof_file, created_at, status)
                        VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('issdsss', $driver_id, $claim_type, $incident_date, $amount, $description, $proof_path, $status);
            }

            if ($stmt->execute()) {
                $insertSuccess = "<p class='text-success'>Claim request submitted successfully.</p>";
            } else {
                $message = "<p class='text-danger'>Error submitting request: " . $stmt->error . "</p>";
            }
            $stmt->close();
        }
    }

    $driver_id = $_SESSION['id'];
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    $sql = "SELECT * FROM claims WHERE driver_id = ? AND status IN ('Pending', 'Rejected')";
    
    if ($filter == 'week') {
        $sql .= " AND created_at >= NOW() - INTERVAL 1 WEEK";
    } elseif ($filter == 'month') {
        $sql .= " AND created_at >= NOW() - INTERVAL 1 MONTH";
    } elseif ($filter == 'year') {
        $sql .= " AND created_at >= NOW() - INTERVAL 1 YEAR";
    }

    $sql .= " ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $driver_id);
    $stmt->execute();
    $requests = $stmt->get_result();
    $stmt->close();
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
    <title>Claim Request</title>
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

        .alert {
            background: rgba(255, 255, 255, 0.1);
            color: #d1d5db;
            border: none;
            border-radius: 8px;
        } 
        option{
            color: black;
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
 
    <div class="container mt-5">
        <div class="card">
            <h2 class="card-title">Claim Request</h2>
            <div class="alert">
                <?= isset($message) ? $message : '' ?>
                <?= isset($insertSuccess) ? $insertSuccess : '' ?>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                <div class="mb-3">
                    <label for="claim_type" class="form-label">Type of Claim</label>
                    <select class="form-select" id="claim_type" name="claim_type" required>
                        <option value="">--Select--</option>
                        <option value="Toll Fee">Toll Fee</option>
                        <option value="Cancelled Trip">Cancelled Trip</option>
                        <option value="Accident">Accident</option>
                        <option value="Other">Other</option>
                    </select>
                    <div class="invalid-feedback">Please select a claim type.</div>
                </div>
                <div class="mb-3">
                    <label for="incident_date" class="form-label">Date of Incident</label>
                    <input type="date" class="form-control" id="incident_date" name="incident_date" required max="<?= date('Y-m-d') ?>">
                    <div class="invalid-feedback">Please select a valid incident date (today or earlier).</div>
                </div>
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount to Claim (₱)</label>
                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" required>
                    <div class="invalid-feedback">Please provide a valid amount.</div>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="e.g., Details of the claim" required></textarea>
                    <div class="invalid-feedback">Please provide a description of the claim.</div>
                </div>
                <div class="mb-3">
                    <label for="proof" class="form-label">Upload Proof (Optional) <span class="text-muted fs-6">Max: 10MB</span></label>
                    <input type="file" class="form-control" id="proof" name="proof" accept=".jpg,.png,.jpeg,.pdf">
                    <div class="invalid-feedback">Please select a valid file.</div>
                </div>
                <div class="d-flex flex-column gap-2 mt-4">
                    <div class="col-12 col-md-12 mb-2 mb-md-0">
                        <button type="submit" class="btn btn-primary w-100">Submit Claim</button>
                    </div>
                    <div class="col-12 col-md-12 mt-2">
                        <a href="../employee/home.php" class="btn btn-outline-light w-100">Back to Dashboard</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Status -->
        <div class="card mt-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="card-title mb-0">Claim Status</h4>
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
                    <a href="./reimbursements.php" class="btn btn-success">View Reimbursement</a>
                </div>
            </div>
            <?php if ($requests->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Created</th>
                                <th>Claim Type</th>
                                <th>Incident Date</th>
                                <th>Amount (₱)</th>
                                <th>Reimbursement File</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $requests->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    <td><?php echo htmlspecialchars($row['claim_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['incident_date']); ?></td>
                                    <td><?php echo number_format($row['amount'], 2); ?></td>
                                    <td>
                                        <?php if (!empty($row['proof_file'])): ?>
                                            <a href="<?php echo htmlspecialchars($row['proof_file']); ?>" target="_blank">View File</a>
                                        <?php else: ?>
                                            No file
                                        <?php endif; ?>
                                    </td>
                                    <td class="status-<?php echo strtolower($row['status']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                                    </td>
                                    <td>
                                        <form action="../includes/update_claim_status.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="claim_id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">You have no pending or rejected claim requests.</div>
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

        // Validate incident date is not in the future
        document.querySelector('form.needs-validation').addEventListener('submit', function(e) {
            const incidentDate = document.getElementById('incident_date').value;
            const today = new Date().toISOString().split('T')[0];
            if (incidentDate > today) {
                e.preventDefault();
                alert('Incident date cannot be in the future.');
            }
        });
    </script>
</body>
</html>