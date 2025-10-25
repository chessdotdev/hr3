<?php
include '../database/connection.php';
include '../includes/auth.php';
include '../includes/loader.php';
try{
  if (isset($_SESSION["id"]) && is_numeric($_SESSION["id"])) {
    $driver_id = (int) $_SESSION["id"];
    
    $stmt = $conn->prepare("SELECT d.firstname,
                            d.lastname, 
                            d.middlename,
                            d.contact_no, 
                            d.email, 
                            d.plate_number,
                            d.vehicle_type, 
                            r.role,
                            s.shift_date,
                            s.shift_start,
                            s.shift_end,
                            s.shift_type
                            FROM driver d 
                            LEFT JOIN role r ON d.role_id = r.id 
                            LEFT JOIN schedule s ON d.id = s.driver_id
                            WHERE d.id = ?  ORDER BY s.scheduled_at DESC, s.id DESC LIMIT 1");
                            
    $stmt->bind_param("i", $driver_id);
    $stmt->execute();
    $result = $stmt->get_result(); 

    if ($row = $result->fetch_assoc()) {
        $driver = $row;
    } else {
        // echo "Session ID" . $driver_id;
    }

    $stmt->close();
} else {
    // echo "session ID is missing.";
}

}catch (Exception $e) {
  echo "Exception: " . $e->getMessage();
  $message = "<p class='text-danger'>An unexpected error occurred. Please try again later.</p>";
}
finally{
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Schedule</title>
  <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

    .info-pair {
      display: flex;
      gap: 10px;
      font-size: 0.95rem;
      margin-bottom: 12px;
    }

    .info-pair strong {
      min-width: 120px;
      color: #d1d5db;
      font-weight: 600;
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
    .table td, .table th {
      color: #ffffff;
      background-color: #2d3748; 
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
      .info-pair {
        flex-direction: column;
        margin-bottom: 8px;
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
      .info-pair strong {
        min-width: 100px;
      }
    }
  </style>
</head>
<body>
  <!-- Employee Information -->
  <div class="container my-5">
  <div class="card">
    <h2 class="card-title">Employee Information</h2>
    <div class="row">
      <div class="col-md-6">
        <div class="info-pair"><strong>First Name:</strong> <?= ucfirst(htmlspecialchars($driver['firstname'] ?? '')); ?></div>
        <div class="info-pair"><strong>Last Name:</strong> <?= ucfirst(htmlspecialchars($driver['lastname'] ?? '')); ?></div>
        <div class="info-pair"><strong>Middle Name:</strong> <?= ucfirst(htmlspecialchars($driver['middlename'] ?? '')); ?></div>
        <div class="info-pair"><strong>Vehicle Type:</strong> <?= ucfirst(htmlspecialchars($driver['vehicle_type'] ?? '')); ?></div>
      </div>
      <div class="col-md-6">
        <div class="info-pair"><strong>Contact No:</strong> <?= ucfirst(htmlspecialchars($driver['contact_no'] ?? '')); ?></div>
        <div class="info-pair"><strong>Email:</strong> <?= ucfirst(htmlspecialchars($driver['email'] ?? '')); ?></div>
        <div class="info-pair"><strong>Plate Number:</strong> <?= ucfirst(htmlspecialchars($driver['plate_number'] ?? '')); ?></div>
        <div class="info-pair"><strong>Role:</strong> <?= ucfirst(htmlspecialchars($driver['role'] ?? '')); ?></div>
      </div>
    </div> 
    <h2 class="mb-4 mt-4">Schedule</h2>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th scope="col">Time</th>
            <th scope="col">Type</th>
            <th scope="col">Date</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <?= ucfirst(htmlspecialchars($driver['shift_start'] ?? '')) . ' - ' . ($driver['shift_end'] ?? '') ?>
            </td>
            <td><?= ucfirst(htmlspecialchars($driver['shift_type'] ?? '')); ?></td>
            <td><?= ucfirst(htmlspecialchars($driver['shift_date'] ?? '')); ?></td>
            <td></td>
          </tr>
        </tbody>
      </table>
    </div>

    <a href="../employee/home.php" class="btn btn-outline-light mt-3">Back to Dashboard</a>
  </div>
</div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    </body>

</html>