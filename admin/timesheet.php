<?php
include '../includes/header.php';
require '../database/connection.php';

// Filters
$range_type = isset($_GET['range_type']) ? $_GET['range_type'] : 'week';
$search_id  = isset($_GET['search_id']) ? trim($_GET['search_id']) : '';

// Date range
$start_date = $end_date = date('Y-m-d');
if($range_type == 'week'){
    $start_date = date('Y-m-d', strtotime('monday this week'));
    $end_date   = date('Y-m-d', strtotime('sunday this week'));
}elseif($range_type == 'month'){
    $start_date = date('Y-m-01');
    $end_date   = date('Y-m-t');
}elseif($range_type == 'year'){
    $start_date = date('Y-01-01');
    $end_date   = date('Y-12-31');
}

$timesheets = []; // default empty
$has_results = false;

// Only run query if user searches
if(!empty($search_id)) {
    $query = "SELECT d.id AS driver_id, d.firstname, DATE(l.time_in) AS work_date,
              SUM(TIMESTAMPDIFF(MINUTE, l.time_in, l.time_out)/60) AS hours_worked
              FROM driver d
              JOIN employee_time_logs l ON d.id = l.driver_id
              WHERE DATE(l.time_in) BETWEEN ? AND ? AND d.id = ?
              GROUP BY d.id, DATE(l.time_in)
              ORDER BY d.firstname ASC, work_date ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $start_date, $end_date, $search_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while($row = $result->fetch_assoc()){
        $timesheets[] = $row;
    }
    $has_results = true;
}

// Excel export
if(isset($_POST['export_excel'])){
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=timesheet.xls");
    echo "Employee Name\tDate\tHours Worked\n";
    foreach($timesheets as $row){
        echo "{$row['firstname']}\t{$row['work_date']}\t".number_format($row['hours_worked'],2)."\n";
    }
    exit();
}

?>
<div class="content">
<div class="container mt-5">
<h1>Timesheet Management</h1>

<!-- Search Form -->
<form method="GET" class="mb-3 d-flex gap-2">
    <select name="range_type" class="form-control">
        <option value="week" <?= $range_type=='week'?'selected':''; ?>>This Week</option>
        <option value="month" <?= $range_type=='month'?'selected':''; ?>>This Month</option>
        <option value="year" <?= $range_type=='year'?'selected':''; ?>>This Year</option>
    </select>
    <input type="number" name="search_id" placeholder="Search by Employee ID" class="form-control" value="<?= htmlspecialchars($search_id); ?>">
    <button type="submit" class="btn btn-primary">Search</button>
    <a href="timesheet.php" class="btn btn-secondary">Reset</a>
</form>

<!-- Display Message or Table -->
<?php if(!$has_results && empty($search_id)): ?>
    <div class="alert alert-info text-center">Please search for an employee to view their timesheet.</div>
<?php elseif($has_results && count($timesheets) == 0): ?>
    <div class="alert alert-warning text-center"> No attendance records found for this employee in the selected period.</div>
<?php elseif(count($timesheets) > 0): ?>
    <table class="table table-bordered table-striped mt-3">
        <thead class="table-secondary">
            <tr>
                <th>Employee Name</th>
                <th>Date</th>
                <th>Hours Worked</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($timesheets as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['firstname']); ?></td>
                <td><?= htmlspecialchars($row['work_date']); ?></td>
                <td><?= number_format($row['hours_worked'], 2); ?> hrs</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Export Excel -->
    <form method="POST">
        <input type="hidden" name="range_type" value="<?= $range_type; ?>">
        <input type="hidden" name="search_id" value="<?= htmlspecialchars($search_id); ?>">
        <button type="submit" name="export_excel" class="btn btn-success">Export Excel</button>
    </form>
<?php endif; ?>

</div>
</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
