<?php
include '../includes/auth.php';
include '../database/connection.php';
include '../includes/loader.php';

try {
    $driver_id = $_SESSION['id'];
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    $sql = "SELECT * FROM claims WHERE driver_id = ? AND status = 'Approved'";
    
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
    <title>Reimbursement Status</title>
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

        .status-approved {
            color: #22c55e;
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

        /* Mobile-friendly table styling from provided CSS */
        @media (max-width: 768px) {
            .table thead {
                display: none;
            }

            .table, .table tbody, .table tr, .table td {
                display: block;
                width: 100%;
            }

            .table tr {
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 0.5rem;
                padding: 0.5rem;
                background-color: rgba(255, 255, 255, 0.08);
            }

            .table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }

            .table td::before {
                content: attr(data-label);
                position: absolute;
                left: 1rem;
                top: 0.5rem;
                font-weight: bold;
                white-space: nowrap;
                text-align: left;
            }
        }

        @media (max-width: 576px) {
            .table-responsive {
                font-size: 0.9rem;
            }

            .btn-sm {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
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
    <div class="navbar">
        <div class="nav-item"><a href="../employee/home.php">Dashboard</a></div>
        <div class="nav-item"><a href="leave.php">Leave Request</a></div>
        <div class="nav-item"><a href="myclaim.php">Claim Request</a></div>
        <div class="nav-item"><a href="shift_request.php">Shift Request</a></div>
        <div class="nav-item"><a href="../logout.php">Logout</a></div>
    </div>

    <!-- Reimbursement Status -->
    <div class="container mt-5">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="card-title mb-0">Reimbursement</h4>
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
                    <a href="./claim.php" class="btn btn-success">Submit New Claim</a>
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $requests->fetch_assoc()): ?>
                                <tr>
                                    <td data-label="Created"><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    <td data-label="Claim Type"><?php echo htmlspecialchars($row['claim_type']); ?></td>
                                    <td data-label="Incident Date"><?php echo htmlspecialchars($row['incident_date']); ?></td>
                                    <td data-label="Amount"><?php echo number_format($row['amount'], 2); ?></td>
                                    <td data-label="Reimbursement File">
                                        <?php if (!empty($row['proof_file'])): ?>
                                            <a href="<?php echo htmlspecialchars($row['proof_file']); ?>" target="_blank">View File</a>
                                        <?php else: ?>
                                            No file
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Status" class="status-approved">
                                        <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">You have no approved reimbursement requests.</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>