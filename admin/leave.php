  <?php
  include '../includes/header.php';
  require '../database/connection.php';

  $sql = "SELECT * FROM leave_request WHERE status = 'Pending'";
  $result = $conn->query($sql);
  ?>
  <link rel="stylesheet" href="../assets/css/shift.css">

  <div class="content">
    <h2 class="mb-4">Driver Shift Requests</h2>
    <div class="container-fluid my-4">
      <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
          <table class="table table-striped table-hover table-bordered">
            <thead class="table-secondary">
              <tr>
                <th scope="col">Driver ID</th>
                <th scope="col">Reason</th>
                <th scope="col">Date</th>
                <th>Created</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td data-label="Driver ID"><?= htmlspecialchars($row['driver_id']) ?></td>
                  <td data-label="Reason"><?= htmlspecialchars($row['reason']) ?></td>
                  <td data-label="Date"><?= htmlspecialchars($row['start_date']) . " - " . htmlspecialchars($row['end_date'] ?? 'no specific date'); ?></td>
                  <td data-label="Created"><?= htmlspecialchars($row['created_at']) ?></td>
                  <td class="alert alert-warning" data-label="Status"><?= htmlspecialchars($row['status']) ?></td>
                  <td data-label="Actions">
                  <form action="../includes/update_leave_status.php" method="POST" style="display:inline;">
                      <input type="hidden" name="shift_id" value="<?= $row['id'] ?>">
                      <input type="hidden" name="action" value="approve">
                      <button type="submit" class="btn btn-sm btn-success me-1">Approve</button>
                    </form>
                    <form action="../includes/update_leave_status.php" method="POST" style="display:inline;">
                      <input type="hidden" name="shift_id" value="<?= $row['id'] ?>">
                      <input type="hidden" name="action" value="reject">
                      <button type="submit" class="btn btn-sm btn-danger me-1" >Reject</button>
                    </form>
                  <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal<?= $row['id'] ?>">View</button>
                  </td>
                </tr>

                <!-- Modal -->
                <div class="modal fade" id="viewModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="viewModalLabel<?= $row['id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="viewModalLabel<?= $row['id'] ?>">Leave Request Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <p><strong>Driver ID:</strong> <?= htmlspecialchars($row['driver_id']) ?></p>
                        <p><strong>Reason:</strong> <?= htmlspecialchars($row['reason']) ?></p>
                        <p><strong>Start Date:</strong> <?= htmlspecialchars($row['start_date']) ?></p>
                        <p><strong>End Date:</strong> <?= htmlspecialchars($row['end_date'] ?? 'No specific date') ?></p>
                        <p><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
                        <?php if (!empty($row['documents'])): ?>
                          <p><strong>Document:</strong> <a href="<?= htmlspecialchars($row['documents']) ?>" target="_blank">View File</a></p>
                        <?php endif; ?>
                        <?= (!empty($row['documents']) && in_array(strtolower(pathinfo($row['documents'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                          ? '<img src="' . htmlspecialchars($row['documents']) . '" alt="document image" width="150">'
                          : 'No Document'; ?> <p><strong>Created At:</strong> <?= htmlspecialchars($row['created_at']) ?></p>

                      </div>
                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-info">No shift requests found.</div>
      <?php endif; ?>
    </div>
  </div>

  <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
  </body>

  </html>