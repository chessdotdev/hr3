<?php
include '../includes/header.php';
require '../database/connection.php';

$sql = "SELECT 
    sr.shift_id,
    sr.driver_id,
    sr.shift_date,
    sr.shift_start,
    sr.shift_end,
    sr.shift_type,
    sr.date,
    sr.status,
    s.scheduled_at
FROM shift_req AS sr
LEFT JOIN schedule AS s 
    ON sr.driver_id = s.driver_id 
    AND sr.shift_date = s.shift_date
WHERE sr.status = 'pending'
";
$result = $conn->query($sql);


?>
<div class="content">
  <h2 class="mb-4">Driver Shift Requests</h2>
  <div class="container-fluid my-4">
    <?php if ($result && $result->num_rows > 0): ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered">
          <thead class="table-secondary">
            <tr>
              <th scope="col">Shift Date</th>
              <th scope="col">Shift Start</th>
              <th scope="col">Shift End</th>
              <th scope="col">Shift Type</th>
              <th scope="col">Date</th>
              <th scope="col">Status</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <?php while ($row = $result->fetch_assoc()): ?>
                <td data-label="Shift Date"><?= htmlspecialchars($row['shift_date']) ?></td>
                <td data-label="Shift Start"><?= htmlspecialchars($row['shift_start']) ?></td>
                <td data-label="shift End"><?= htmlspecialchars($row['shift_end']) ?></td>
                <td data-label="Shift Type"><?= htmlspecialchars($row['shift_type']) ?></td>
                <td data-label="Date"><?= htmlspecialchars($row['date']) ?></td>
                <td data-label="Status"><?= htmlspecialchars($row['status']) ?></td>
                <td data-label="Actions">
                  <form action="../includes/update_shift_status.php" method="POST" style="display:inline;">
                    <input type="hidden" name="shift_id" value="<?= $row['shift_id'] ?>">
                    <input type="hidden" name="action" value="approve">
                    <button type="submit" class="btn btn-sm btn-success me-1">Approve</button>
                  </form>
                  <form action="../includes/update_shift_status.php" method="POST" style="display:inline;">
                    <input type="hidden" name="shift_id" value="<?= $row['shift_id'] ?>">
                    <input type="hidden" name="action" value="reject">
                    <button type="submit" class="btn btn-sm btn-danger me-1" >Reject</button>
                  </form>
                  <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal1">View</button>
                </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-info">No shift requests found.</div>
    <?php endif; ?>
  </div>

  <!-- Shift Detail Modals (unchanged) -->
  <div class="modal fade" id="viewModal1" tabindex="-1" aria-labelledby="viewModalLabel1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Shift Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p><strong>Shift ID:</strong> 1</p>
          <p><strong>Shift Date:</strong> 2025-10-01</p>
          <p><strong>Shift Start:</strong> 08:00 AM</p>
          <p><strong>Shift End:</strong> 04:00 PM</p>
          <p><strong>Shift Type:</strong> Day</p>
          <p><strong>Status:</strong> Approved</p>
          <p><strong>Driver ID:</strong> DR001</p>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>