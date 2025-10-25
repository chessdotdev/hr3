<?php
include '../includes/auth.php';
include '../database/connection.php';
include '../includes/loader.php';

$driver_id = $_SESSION['id'];
$time_in = null;
$active_session = false;

try {
    $sql = "SELECT time_in FROM employee_time_logs WHERE driver_id = ? AND time_out IS NULL ORDER BY time_in DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $driver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $time_in = $row['time_in'];
        $active_session = true;
    }
    $stmt->close();
} catch (Exception $e) {
    $message = "<p class='text-danger'>Error fetching time log: " . $e->getMessage() . "</p>";
} finally {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: #1f2937;
            color: #ffffff;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
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

        .hero-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            max-width: 900px;
            margin: 40px auto;
            padding: 32px;
            text-align: center;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .time-btn {
            font-size: 1.25rem;
            padding: 16px 32px;
            border-radius: 12px;
            background: #22c55e;
            border: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            width: 200px;
            margin: 0 auto;
        }

        .time-btn:hover {
            background: #16a34a;
            transform: scale(1.05);
        }

        .time-btn.success {
            background: #10b981;
            animation: pulse 0.5s ease;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .timer {
            font-size: 1.5rem;
            font-weight: 600;
            color: #60a5fa;
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 10px;
            display: inline-block;
        }

        .card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 24px;
            transition: all 0.3s ease;
            backdrop-filter: blur(6px);
            color: #ffffff;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card-icon {
            font-size: 2.5rem;
            color: #60a5fa;
            margin-bottom: 12px;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .card-text {
            color: #d1d5db;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: #3b82f6;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #1e40af;
        }

        .section-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin: 40px 0 20px;
            text-align: center;
        }

        .container {
            max-width: 1000px;
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
            .time-btn {
                width: 180px;
                font-size: 1.1rem;
            }
            .timer {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 475px) {
            .nav-item a {
                font-size: 0.8rem;
                padding: 6px 10px;
            }
            .hero-section {
                padding: 24px;
            }
            .time-btn {
                width: 160px;
                font-size: 1rem;
            }
            .timer {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="nav-item"><a href="#home">Time</a></div>
    <div class="nav-item"><a href="#shift">Shifts</a></div>
    <div class="nav-item"><a href="#leave">Leaves</a></div>
    <div class="nav-item"><a href="#timesheet">Timesheets</a></div>
    <div class="nav-item"><a href="#claims">Claims</a></div>
    <div class="nav-item">
      <form action="../includes/logout.php" method="POST">
        <button type="submit" class="btn btn-outline-light btn-sm rounded-pill">Logout</button>
      </form>
    </div>
  </nav>

<!-- Time and Attendance -->
<div class="hero-section" id="home">
        <h1 class="mb-3">Dashboard</h1>
        <p class="mb-4">Go online/out</p>
        <form action="../includes/attendance.php" method="POST" onsubmit="return confirm('Confirm <?php echo $active_session ? 'Clock Out' : 'Clock In'; ?>?');">
            <input type="hidden" name="action" value="<?php echo $active_session ? 'clock_out' : 'clock_in'; ?>">
            <button type="submit" class="time-btn btn <?php echo $active_session ? 'success' : ''; ?>">
                <i class="fas fa-clock"></i> <span id="btn-text"><?php echo $active_session ? 'Clock Out' : 'Clock In'; ?></span>
            </button>
        </form>
        <?php if ($active_session && $time_in): ?>
            <div class="timer" id="timer">00:00:00</div>
            <script>
                // Timer logic
                const startTime = new Date('<?php echo $time_in; ?>').getTime();
                function updateTimer() {
                    const now = new Date().getTime();
                    const elapsed = now - startTime;
                    const hours = Math.floor(elapsed / (1000 * 60 * 60)).toString().padStart(2, '0');
                    const minutes = Math.floor((elapsed % (1000 * 60 * 60)) / (1000 * 60)).toString().padStart(2, '0');
                    const seconds = Math.floor((elapsed % (1000 * 60)) / 1000).toString().padStart(2, '0');
                    document.getElementById('timer').textContent = `${hours}:${minutes}:${seconds}`;
                }
                updateTimer();
                setInterval(updateTimer, 1000);
            </script>
        <?php endif; ?>
        <?php if (isset($message)): ?>
            <div class="alert alert-danger mt-3"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>
  <!-- Main Content -->
  <div class="container my-5">
    <!-- Shift and Schedule Management -->
    <h2 class="section-title" id="shift">Shifts & Schedules</h2>
    <div class="row">
      <div class="col-md-6 mb-4">
        <div class="card text-center">
          <i class="fas fa-calendar-plus card-icon"></i>
          <h5 class="card-title">Manage Shifts</h5>
          <p class="card-text">Submit Shift and check Status.</p>
          <a href="shift.php" class="btn btn-primary">Manage Now</a>
        </div>
      </div>
      <div class="col-md-6 mb-4">
        <div class="card text-center">
          <i class="fas fa-calendar-week card-icon"></i>
          <h5 class="card-title">View Schedules</h5>
          <p class="card-text">Check your schedules.</p>
          <a href="schedule.php" class="btn btn-primary">View Calendar</a>
        </div>
      </div>
    </div>

    <!-- Leave Management -->
    <h2 class="section-title" id="leave">Leave Management & Timesheets</h2>
    <div class="row">
      <div class="col-md-6 mb-4">
        <div class="card text-center">
          <i class="fas fa-umbrella-beach card-icon"></i>
          <h5 class="card-title">Apply for Leave</h5>
          <p class="card-text">Submit leave requests and check Status.</p>
          <a href="leave.php" class="btn btn-primary">Apply Now</a>
        </div>
      </div>
      <div class="col-md-6 mb-4">
        <div class="card text-center">
        <i class="fas fa-table card-icon"></i>
          <h5 class="card-title">View Timesheets</h5>
          <p class="card-text">Review and adjust your logged hours.</p>
          <a href="timesheet.php" class="btn btn-primary">View Timesheets</a>
        </div>
      </div>
    </div>


    <!-- Claims Management -->
    <h2 class="section-title" id="claims">Claims & Reimbursements</h2>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card text-center">
                    <i class="fas fa-receipt card-icon"></i>
                    <h5 class="card-title">Submit Claims</h5>
                    <p class="card-text">Submit claim and check status.</p>
                    <a href="claim.php" class="btn btn-primary">Submit Claim</a>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card text-center">
                    <i class="fas fa-list card-icon"></i>
                    <h5 class="card-title">View Reimbursements</h5>
                    <p class="card-text">Check the approved claims.</p>
                    <a href="reimbursements.php" class="btn btn-primary">View Reimbursements</a>
                </div>
            </div>
        </div>
    </div>

  
  <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script>
    // Update button text based on time status
    function updateButtonText() {
      fetch('../includes/time_status.php')
        .then(response => response.text())
        .then(status => {
          const btn = document.querySelector('.time-btn');
          document.getElementById('btn-text').textContent = status;
          if (status.includes('Success')) {
            btn.classList.add('success');
            setTimeout(() => btn.classList.remove('success'), 1000);
          }
        });
    }
    updateButtonText();
    setInterval(updateButtonText, 60000); // Update every minute

    //geolocation for clock-in/out
    function getGeolocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          pos => console.log(`Lat: ${pos.coords.latitude}, Lon: ${pos.coords.longitude}`),
          err => console.warn('Geolocation not available:', err.message)
        );
      }
    }
  </script>
</body>
</html>