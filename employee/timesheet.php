<?php
include '../includes/auth.php';
include '../database/connection.php';
include '../includes/loader.php';
include '../functions/formatHoursWork.php';

try {
    $driver_id = $_SESSION['id'];

    $filter = $_GET['filter'] ?? 'all';
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
    $limit = 5;
    $offset = ($page - 1) * $limit;

    $whereClause = "WHERE driver_id = $driver_id";

    if ($filter == 'today') {
        $today_start = date('Y-m-d 00:00:00');
        $today_end = date('Y-m-d 23:59:59');
        $whereClause .= " AND created_at BETWEEN '$today_start' AND '$today_end'";
    } elseif ($filter == 'week') {
        $week_start = date('Y-m-d 00:00:00', strtotime('monday this week'));
        $week_end = date('Y-m-d 23:59:59', strtotime('sunday this week'));
        $whereClause .= " AND created_at BETWEEN '$week_start' AND '$week_end'";
    } elseif ($filter == 'month') {
        $month_start = date('Y-m-01 00:00:00');
        $month_end = date('Y-m-t 23:59:59');
        $whereClause .= " AND created_at BETWEEN '$month_start' AND '$month_end'";
    } elseif ($filter == 'year') {
        $year_start = date('Y-01-01 00:00:00');
        $year_end = date('Y-12-31 23:59:59');
        $whereClause .= " AND created_at BETWEEN '$year_start' AND '$year_end'";
    }

    $count_query = "SELECT COUNT(DISTINCT DATE(created_at)) as total FROM employee_time_logs $whereClause";
    $count_result = $conn->query($count_query);
    $total_rows = $count_result->fetch_assoc()['total'] ?? 0;
    $total_pages = ceil($total_rows / $limit);

    $query = "SELECT created_at AS work_date,
                     SUM(TIMESTAMPDIFF(MINUTE, time_in, time_out)/60) AS hours_worked
              FROM employee_time_logs
              $whereClause
              GROUP BY DATE(created_at)
              ORDER BY created_at ASC
              LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $timesheets = [];
    $total = 0;
    while ($row = $result->fetch_assoc()) {
        $timesheets[] = $row;
        $total += floatval($row['hours_worked']);
    }

    $total_hours = floor($total);
    $total_minutes = round(($total - $total_hours) * 60);
    $workedHoursTotal = "{$total_hours} hrs {$total_minutes} mins";
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
    <title>Timesheet</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <style>
        body {
            background: #1f2937;
            color: #ffffff;
            font-family: 'Inter', sans-serif;
        }

        .container {
            max-width: 900px;
            padding: 20px;
        }

        .card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 24px;
        }

        .form-select,
        .btn {
            border-radius: 8px;
        }

        .table {
            background: rgba(255, 255, 255, 0.08);
            color: #d1d5db;
        }

        .table thead {
            background: rgba(255, 255, 255, 0.1);
        }

        .pagination .page-link {
            background: #374151;
            color: #ffffff;
            border: none;
        }

        .pagination .page-link:hover {
            background: #3b82f6;
        }

        .pagination .active .page-link {
            background: #3b82f6;
            border: none;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <div class="card">
            <h2 class="card-title">My Timesheet</h2>
            <div class="alert">
                <?= isset($message) ? "$message" : '' ?>
            </div>
            <!-- Filter Form -->
            <form method="GET" class="d-flex gap-3 mb-3">
                <select name="filter" class="form-select w-auto">
                    <option value="today" <?= $filter === 'today' ? 'selected' : '' ?>>Today</option>
                    <option value="week" <?= $filter === 'week' ? 'selected' : '' ?>>This Week</option>
                    <option value="month" <?= $filter === 'month' ? 'selected' : '' ?>>This Month</option>
                    <option value="year" <?= $filter === 'year' ? 'selected' : '' ?>>This Year</option>
                </select>

                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="timesheet.php" class="btn btn-outline-light">Reset</a>
            </form>

            <?php if (count($timesheets) === 0): ?>
                <div class="alert alert-info">No timesheet data available for this period.</div>
            <?php else: ?>
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Hours Worked</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($timesheets as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['work_date']) ?></td>
                                <td><?= formatHoursMinutes(floatval($row['hours_worked'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td>Total Hours</td>
                            <td><?= $workedHoursTotal ?></td>
                        </tr>

                    </tbody>

                </table>

                <?php if ($total_pages > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center mt-4">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?filter=<?= urlencode($filter) ?>&page=<?= $i ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>

            <div class="mt-4">
                <a href="../includes/download_timesheet_excel.php?filter=<?= urlencode($filter) ?>" class="btn btn-success w-100">Download Excel</a>

                <a href="../employee/home.php" class="btn btn-outline-light w-100">Back to Dashboard</a>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>