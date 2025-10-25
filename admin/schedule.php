<?php
include '../includes/header.php';
require '../database/connection.php';

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "
    SELECT 
        driver.id AS driver_id,
        driver.firstname,
        driver.lastname,
        driver.middlename,
        driver.contact_no,
        schedule.id AS schedule_id,
        schedule.shift_date,
        schedule.shift_start,
        schedule.shift_end
    FROM driver
    INNER JOIN schedule ON driver.id = schedule.driver_id
    WHERE schedule.shift_date IS NULL
      AND schedule.shift_start IS NULL
      AND schedule.shift_end IS NULL
      AND driver.id LIKE '%$search%'
";
$result = $conn->query($sql);
?>
<link rel="stylesheet" href="../assets/css/shift.css">

<div class="content container mt-3">

    <form method="GET" class="mb-3 d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Search by Driver ID" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
 <div class="table-responsive">
          <table class="table table-bordered table-striped">
    <thead class="table-secondary">
        <tr>
            <th>Driver ID</th>
            <th>Driver Name</th>
            <th>Contact No.</th>
            <th>Shift Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0) : ?>
            <?php while ($row = $result->fetch_assoc()) : 
                $shift_date  = $row['shift_date']  ?? '-';
                $shift_start = $row['shift_start'] ?? '-';
                $shift_end   = $row['shift_end']   ?? '-';
            ?>
            <tr>
                <td data-label="Driver ID"><?= htmlspecialchars($row['driver_id']) ?></td>
                <td data-label="Firstname"><?= ucwords(htmlspecialchars($row['firstname'] . " " . $row['lastname'] . " " . $row['middlename'])) ?></td>
                <td data-label="Contact Number"><?= htmlspecialchars($row['contact_no']) ?></td>
                <td data-label="Date"><?= htmlspecialchars($shift_date) ?></td>
                <td data-label="Start"><?= htmlspecialchars($shift_start) ?></td>
                <td data-label="End"><?= htmlspecialchars($shift_end) ?></td>
                <td data-label="Actions">
                    <button 
                        class="btn btn-sm btn-warning editBtn" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editModal"
                        data-id="<?= htmlspecialchars($row['schedule_id']) ?>"
                        data-date="<?= htmlspecialchars($row['shift_date']) ?>"
                        data-start="<?= htmlspecialchars($row['shift_start']) ?>"
                        data-end="<?= htmlspecialchars($row['shift_end']) ?>"
                    >
                        Add Schedule
                    </button>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr><td colspan="7" class="text-center">No new drivers</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</div>
</div>
<!-- Edit Schedule Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="../includes/update_schedule.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Schedule</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="schedule_id" id="edit_id">
        <div class="mb-3">
          <label class="form-label">Shift Date</label>
          <input type="date" name="shift_date" id="edit_date" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Start Time</label>
          <input type="time" name="start_time" id="edit_start" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">End Time</label>
          <input type="time" name="end_time" id="edit_end" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Update</button>
      </div>
    </form>
  </div>
</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Script to populate modal -->
<script>
document.querySelectorAll('.editBtn').forEach(button => {
  button.addEventListener('click', () => {
    document.getElementById('edit_id').value = button.getAttribute('data-id');
    document.getElementById('edit_date').value = button.getAttribute('data-date') || '';
    document.getElementById('edit_start').value = button.getAttribute('data-start') || '';
    document.getElementById('edit_end').value = button.getAttribute('data-end') || '';
  });
});
</script>

</body>
</html>
