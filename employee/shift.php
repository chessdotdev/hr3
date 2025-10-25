<?php
include '../includes/auth.php';
include '../database/connection.php';
include '../includes/loader.php';
include '../functions/duplicate_request.php';

try {
  $message = '';
  $insertSuccess = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $driver_id = $_SESSION['id'];

    $shift_date = $_POST['shift_date'] ?? '';
    $shift_start = $_POST['shift_start'] ?? '';
    $shift_end = $_POST['shift_end'] ?? '';
    $status = 'Pending';
    if (empty($shift_date) || empty($shift_start) || empty($shift_end)) {
      $message = "<p class='text-danger'>Please fill in all required fields.</p>";
    } else {
      $shift_date = filter_input(INPUT_POST, 'shift_date', FILTER_SANITIZE_SPECIAL_CHARS);
      $shift_start = filter_input(INPUT_POST, 'shift_start', FILTER_SANITIZE_SPECIAL_CHARS);
      $shift_end = filter_input(INPUT_POST, 'shift_end', FILTER_SANITIZE_SPECIAL_CHARS);
      $status = 'Pending';

      include '../includes/timeFormat.php'; 
      checkDuplicate('shift_req');

      if ($pendingCount > 0) {
        $message = "<p class='text-danger'>You already have a pending shift request. Please wait for it to be reviewed.</p>";
      } else {
        $stmt = $conn->prepare("INSERT INTO shift_req 
          (shift_date, shift_start, shift_end, shift_type, status, driver_id)
          VALUES (?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssssi", $shift_date, $start, $end, $shift_type, $status, $driver_id);

        if ($stmt->execute()) {
          $insertSuccess = "<p class='text-success'>Shift request submitted successfully.</p>";
        } else {
          $message = "<p class='text-danger'>Error submitting request: " . $stmt->error . "</p>";
        }
      }
    }
  }

  // --- Filter---
  $driver_id = $_SESSION['id'];
  $filter = $_GET['filter'] ?? 'all';

  $whereClause = "WHERE driver_id = ?";
  switch ($filter) {
    case 'week':
      $whereClause .= " AND WEEK(date) = WEEK(CURDATE()) AND YEAR(date) = YEAR(CURDATE())";
      break;
    case 'month':
      $whereClause .= " AND MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())";
      break;
    case 'year':
      $whereClause .= " AND YEAR(date) = YEAR(CURDATE())";
      break;
  }

  $sql = "SELECT shift_date, shift_start, shift_end, shift_type, status, date 
          FROM shift_req 
          $whereClause 
          ORDER BY date DESC";

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
  <title>Shift Request</title>
  <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
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

    .form-control {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid #4b5563;
      color: #ffffff;
      border-radius: 8px;
    }

    .form-control:focus {
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

  <!-- Shift Request Form -->
  <div class="container mt-5">
    <div class="card">
      <h2 class="card-title">Shift Request</h2>
      <div class="alert">
        <?= isset($message) ? "$message" : '' ?>
        <?= isset($insertSuccess) ? "$insertSuccess" : '' ?>
      </div>


      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="needs-validation" novalidate>
        <div class="mb-3">
          <label for="shift_date" class="form-label">Shift Date</label>
          <input type="date" class="form-control" id="shift_date" name="shift_date" required min="<?php echo date('Y-m-d'); ?>">
          <div class="invalid-feedback">Please select a valid shift date (today or later).</div>
        </div>
        <div class="mb-3">
          <label for="shift_start" class="form-label">Shift Start Time</label>
          <input type="time" class="form-control" id="shift_start" name="shift_start" required>
          <div class="invalid-feedback">Please select a start time.</div>
        </div>
        <div class="mb-3">
          <label for="shift_end" class="form-label">Shift End Time</label>
          <input type="time" class="form-control" id="shift_end" name="shift_end" required>
          <div class="invalid-feedback">Please select an end time.</div>
        </div>
        <div class="d-flex flex-column gap-2 mt-4">
          <div class="col-12 col-md-12 mb-2 mb-md-0">
            <button type="submit" class="btn btn-primary w-100">Submit Shift Request</button>
          </div>
          <div class="col-12 col-md-12 mt-2">
            <a href="../employee/home.php" class="btn btn-outline-light w-100">Back to Dashboard</a>
          </div>
        </div>
      </form>
    </div>

    <!-- Shift Request History -->
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
                <th>Shift Date</th>
                <th>Shift Type</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $requests->fetch_assoc()): ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['date']); ?></td>
                  <td><?php echo htmlspecialchars($row['shift_date']); ?></td>
                  <td><?php echo htmlspecialchars(ucfirst($row['shift_type'])); ?></td>
                  <td><?php echo htmlspecialchars($row['shift_start']); ?></td>
                  <td><?php echo htmlspecialchars($row['shift_end']); ?></td>
                  <td class="status-<?php echo strtolower($row['status']); ?>">
                    <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-info">Your schedule has not been assigned yet. Please wait for further updates.</div>
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

    // Basic client-side conflict detection
    document.querySelector('form').addEventListener('submit', function(e) {
      const startTime = document.getElementById('shift_start').value;
      const endTime = document.getElementById('shift_end').value;
      if (startTime && endTime && startTime >= endTime) {
        e.preventDefault();
        alert('End time must be after start time.');
      }
    });
  </script>
</body>

</html>