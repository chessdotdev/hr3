<?php
include 'loader.php';
include '../includes/auth_admin.php';
include '../includes/notificationsCount.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Administrator</title>
  <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      overflow-x: hidden;
      font-family: 'Inter', Arial, sans-serif;
      background-color: #F1F5F9;
      color: #1E293B;
      font-size: 0.9rem;
    }

    .sidebar {
      height: 100vh;
      width: 250px;
      position: fixed;
      top: 0;
      left: 0;
      background-color: #FFFFFF;
      padding-top: 1.5rem;
      border-right: 1px solid #E2E8F0;
      transition: transform 0.3s ease;
      z-index: 1000;
      font-size: 0.95rem;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
      overflow: auto;
    
    }

    .sidebar .navbar-brand {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1E293B;
      padding: 0 1.5rem 1rem;
      text-align: center;
    }

    .sidebar hr {
      border-color: #E2E8F0;
      margin: 0.5rem 1rem;
    }

    .sidebar .nav-link {
      margin: 0.3rem 1rem;
      padding: 0.6rem 1rem;
      color: #1E293B;
      border-radius: 8px;
      transition: all 0.3s ease;
      font-weight: 500;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      background-color: #3B82F6;
      color: white;
    }

    .sidebar .text-muted {
      font-size: 0.85rem;
      font-weight: 600;
      color: #64748B;
      padding: 0.5rem 1.5rem;
    }

    .sidebar .logout-btn {
      display: inline-flex;
      align-items: center;
      padding: 0.6rem 1.5rem;
      background-color: #EF4444;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .sidebar .logout-btn:hover {
      background-color: #DC2626;
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar .logout-btn img {
      margin-right: 0.5rem;
    }

    .content {
      margin-left: 260px;
      padding: 2rem;
      min-height: 100vh;
    }

    .navbar {
      background-color: #FFFFFF;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .navbar-brand {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1E293B;
    }

    .navbar-toggler {
      border: none;
    }

    .navbar-toggler:focus {
      outline: none;
      box-shadow: none;
    }

    .navbar-toggler img {
      width: 24px;
      height: 24px;
    }

    .card {
      background: linear-gradient(135deg, #FFFFFF, #F8FAFC);
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      overflow: hidden;
      position: relative;
      width: 100%;
      max-width: 18rem;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }

    .card-body {
      padding: 1.5rem;
      text-align: center;
    }

    .card-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #1E293B;
      margin-bottom: 1rem;
    }

    .card-text {
      font-size: 2rem;
      font-weight: 700;
      color: #3B82F6;
      margin: 0;
      line-height: 1.2;
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, #3B82F6, #60A5FA);
    }

    .nav-folder {
      margin: 0.3rem 1rem;
      padding: 0.6rem 1rem;
      color: #1E293B;
      border-radius: 8px;
      transition: all 0.3s ease;
      font-weight: 500;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: none;
      border: none;
      width: calc(100% - 2rem);
      text-align: left;
    }

    .nav-folder:hover {
      background-color: #F1F5F9;
    }

    .nav-folder[aria-expanded="true"] {
      background-color: #3B82F6;
      color: white;
    }

    .nav-folder[aria-expanded="true"] .arrow {
      transform: rotate(90deg);
    }

    .nav-folder .arrow {
      transition: transform 0.3s ease;
    }

    .nested-menu {
      padding-left: 2rem;
    }

    .nested-menu .nav-link {
      font-size: 0.9rem;
      padding: 0.4rem 1rem;
    }

    @media (max-width: 902px) {
      .sidebar {
        transform: translateX(-100%);
        width: 100%;
        max-width: 250px;
        height: 100vh;
        border-right: none;
      }

      .sidebar.show {
        transform: translateX(0);
      }

      .content {
        margin-left: 0;
        padding: 1rem;
      }

      .navbar-brand {
        font-size: 1.5rem !important;
      }

      .card {
        max-width: 100%;
        margin-bottom: 1rem;
      }
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-light bg-light d-md-none">
    <div class="container-fluid">
      <a class="navbar-brand text-md-center" href="#">Admin</a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#sidebarMenu"
        aria-controls="sidebarMenu"
        aria-expanded="false"
        aria-label="Toggle navigation">
        <img src="../assets/image/menu.png" alt="Menu" width="24" height="24">
      </button>
    </div>
  </nav>

  <!-- Sidebar -->
  <div class="collapse navbar-collapse d-md-block sidebar" id="sidebarMenu">
    <a class="navbar-brand px-3 d-none d-md-block text-center" href="dashboard.php">Admin</a>
    <hr />
    <div class="nav flex-column px-3">
      <h6 class="text-muted mt-2">Management</h6>
      <a class="nav-link" href="dashboard.php">Dashboard</a>
      <a class="nav-link" href="../admin/users.php">Users</a>
      <a class="nav-link" href="../admin/attendance.php">Time and Attendance</a>
      <a class="nav-link" href="../admin/timesheet.php">Timesheet</a>
      <!-- Notification -->
      <button class="nav-folder d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#notificationMenu" aria-expanded="false" aria-controls="notificationMenu">
        <span>Notification</span>
        <?php if ($totalPending > 0): ?>
          <span class="badge bg-danger rounded-pill"><?= $totalPending ?></span>
        <?php endif; ?> <img src="../assets/image/down-arrow.png" alt="Arrow" class="arrow" width="16" height="16">
      </button>
      <div class="collapse nested-menu" id="notificationMenu">
        <a class="nav-link" href="../admin/shift_and_schedule.php">
          Shift
          <?php if ($shift[0] > 0): ?>
            <span class="badge bg-danger rounded-pill"><?= $shift[0] ?></span>
          <?php endif; ?>
        </a>
        <a class="nav-link" href="../admin/schedule.php">Schedule
          <span class="badge bg-danger rounded-pill"><?= $schedule[0] ?></span>

        </a>
        <a class="nav-link" href="../admin/leave.php">
          Leave
          <?php if ($leave[0] > 0): ?>
            <span class="badge bg-danger rounded-pill"><?= $leave[0] ?></span>
          <?php endif; ?>
        </a>
        <a class="nav-link reimburse" href="reimbursement.php">
          Reimbursement
        <?php if ($claims[0] > 0): ?>
            <span class="badge bg-danger rounded-pill"><?= $claims[0]?></span>
          <?php endif; ?>
          </a>
      </div>
    </div>
    <div class="position-absolute bottom-0 start-0 w-100 text-center py-3">
      <form action="../includes/logout.php" method="POST">
        <button type="submit" class="logout-btn">
          <img src="../assets/image/logout.png" alt="logout" width="20" height="20">
          Logout
        </button>
      </form>
    </div>
  </div>

  <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>