<?php
include '../includes/header.php';
include '../database/connection.php';
include '../includes/loader.php';

$sql = "SELECT * FROM claims WHERE status = 'Pending'";
$result = $conn->query($sql);
?>

<link rel="stylesheet" href="../assets/css/shift.css">

    <!-- Content -->
    <div class="content">
        <h2 class="mb-4">Pending Claim Requests</h2>
        <div class="container-fluid my-4">
            <?php if ($result && $result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered">
                        <thead class="table-secondary">
                            <tr>
                                <th scope="col">Driver ID</th>
                                <th scope="col">Claim Type</th>
                                <th scope="col">Incident Date</th>
                                <th scope="col">Amount (₱)</th>
                                <th scope="col">Created</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td data-label="Driver ID"><?= htmlspecialchars($row['driver_id']) ?></td>
                                    <td data-label="Claim Type"><?= htmlspecialchars($row['claim_type']) ?></td>
                                    <td data-label="Incident Date"><?= htmlspecialchars($row['incident_date']) ?></td>
                                    <td data-label="Amount"><?= number_format($row['amount'], 2) ?></td>
                                    <td data-label="Created"><?= htmlspecialchars($row['created_at']) ?></td>
                                    <td data-label="Status">
                                        <span class="status-badge status-<?= strtolower($row['status']) ?>">
                                            <?= htmlspecialchars($row['status']) ?>
                                        </span>
                                    </td>
                                    <td data-label="Actions">
                                        <form action="../includes/update_claim_status.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="claim_id" value="<?= $row['id'] ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-sm btn-success me-1">Approve</button>
                                        </form>
                                        <form action="../includes/update_claim_status.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="claim_id" value="<?= $row['id'] ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-sm btn-danger me-1">Reject</button>
                                        </form>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal<?= $row['id'] ?>">View</button>
                                    </td>
                                </tr>

                                <!-- Modal -->
                                <div class="modal fade" id="viewModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="viewModalLabel<?= $row['id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="viewModalLabel<?= $row['id'] ?>">Claim Request Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="detail-item">
                                                    <span class="detail-label">Driver ID:</span>
                                                    <span class="detail-value"><?= htmlspecialchars($row['driver_id']) ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Claim Type:</span>
                                                    <span class="detail-value"><?= htmlspecialchars($row['claim_type']) ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Incident Date:</span>
                                                    <span class="detail-value"><?= htmlspecialchars($row['incident_date']) ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Amount (₱):</span>
                                                    <span class="detail-value"><?= number_format($row['amount'], 2) ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Description:</span>
                                                    <span class="detail-value"><?= htmlspecialchars($row['description']) ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Status:</span>
                                                    <span class="status-badge status-<?= strtolower($row['status']) ?>">
                                                        <?= htmlspecialchars($row['status']) ?>
                                                    </span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Document:</span>
                                                    <span class="detail-value">
                                                        <?php if (!empty($row['proof_file'])): ?>
                                                            <a href="<?= htmlspecialchars($row['proof_file']) ?>" target="_blank">View File</a>
                                                        <?php else: ?>
                                                            No document uploaded
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                                <?php if (!empty($row['proof_file']) && in_array(strtolower(pathinfo($row['proof_file'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png'])): ?>
                                                    <div class="document-preview">
                                                        <img src="<?= htmlspecialchars($row['proof_file']) ?>" alt="Proof document">
                                                    </div>
                                                <?php elseif (!empty($row['proof_file']) && strtolower(pathinfo($row['proof_file'], PATHINFO_EXTENSION)) === 'pdf'): ?>
                                                    <div class="document-preview">
                                                        <p>No preview available for PDF files.</p>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="detail-item">
                                                    <span class="detail-label">Created At:</span>
                                                    <span class="detail-value"><?= htmlspecialchars($row['created_at']) ?></span>
                                                </div>
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
                <div class="alert alert-info">No pending claim requests found.</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>