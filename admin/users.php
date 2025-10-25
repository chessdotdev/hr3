<?php
include '../includes/header.php';
include '../includes/deleteUser.php';
require '../database/connection.php';
$duplicateMessage = '';
$successMessage = '';
if (isset($_POST['add_user'])) {
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $middlename = mysqli_real_escape_string($conn, $_POST['middlename']);
    $contact_no = mysqli_real_escape_string($conn, $_POST['contact_no']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $status = 'active'; 


  
    $check_sql = "SELECT contact_no FROM admins WHERE contact_no = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $contact_no);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
      $duplicateMessage = "<div class='alert alert-danger'>Contact number already exists!</div>";
    } else {
        $sql = "INSERT INTO admins (firstname, lastname, middlename, contact_no, username, password, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $firstname, $lastname, $middlename, $contact_no, $username, $password, $role, $status);
        
        if ($stmt->execute()) {
          $successMessage = "<div class='alert alert-success'>User added successfully!</div>";
        } else {
            // echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
 
}
$sql = "SELECT 
  admins.admin_id, 
  admins.firstname, 
  admins.lastname, 
  admins.middlename, 
  admins.contact_no, 
  admins.password, 
  role.role 
FROM admins
INNER JOIN role ON admins.role_id = role.id;
";
$result = $conn->query($sql);

?>



<div class="content">
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>User Management</h1>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
    </div>
  

    <!-- Users Table -->
    <table class="table table-bordered table-hover">
    <?=isset($duplicateMessage) ? $duplicateMessage : ''?>
    <?=isset($successMessage) ? $successMessage : ''?>

  <thead class="table-light">
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Firstname</th>
      <th scope="col">Lastname</th>
      <th scope="col">Middlename</th>
      <th scope="col">Contact</th>
      <th scope="col">Role</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody id="userTableBody">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['admin_id']) ?></td>
          <td><?= htmlspecialchars($row['firstname']) ?></td>
          <td><?= htmlspecialchars($row['lastname']) ?></td>
          <td><?= htmlspecialchars($row['middlename']) ?></td>
          <td><?= htmlspecialchars($row['contact_no']) ?></td>
          <td><?= htmlspecialchars($row['role']) ?></td>
          <td>
          <!-- Delete form -->
          <form action="../includes/deleteUser.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
            <input type="hidden" name="delete_user_id" value="<?= $row['admin_id'] ?>">
            <button type="submit" class="btn btn-danger btn-sm" name="delete_user">Delete</button>
          </form>
        </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr>
        <td colspan="4" class="text-center">No users found.</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="addUserForm" method="POST">
              <div class="mb-3">
                <label for="userName" class="form-label">Firstname:</label>
                <input type="text" class="form-control" name="firstname" required>
              </div>
              <div class="mb-3">
                <label for="userName" class="form-label">lastname:</label>
                <input type="text" class="form-control" name="lastname" required>
              </div>
              <div class="mb-3">
                <label for="userName" class="form-label">Middlename:(optional)</label>
                <input type="text" class="form-control" name="middlename" >
              </div>
              <div class="mb-3">
                <label for="userContact" class="form-label">Contact Number</label>
                <input type="tel" class="form-control" name="contact_no" required>
              </div>
              <div class="mb-3">
                <label for="userName" class="form-label">Username:</label>
                <input type="text" class="form-control" name="username" required>
              </div>
              <div class="mb-3">
                <label for="userPassword" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
              </div>

              <div class="mb-3">
            <label for="adminSelect" class="form-label">Choose Admin</label>
            <select class="form-select" id="adminSelect" name="role" required>
              <option value="">-- Select Role --</option>
                <option value="Admin" name="role">Admin</option>
            </select>
          </div>
          
          </div>
        
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="add_user" class="btn btn-primary" onclick="addUser()">Add User</button>
          </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>

  <!-- Bootstrap JS and Popper.js -->
  <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script>
    // function addUser() {
    //   const name = document.getElementById('userName').value;
    //   const contact = document.getElementById('userContact').value;
    //   const password = document.getElementById('userPassword').value;

    //   if (name && contact && password) {
    //     const tableBody = document.getElementById('userTableBody');
    //     const newRow = document.createElement('tr');
    //     const newId = tableBody.children.length + 1;
    //     newRow.innerHTML = `
    //       <td>${newId}</td>
    //       <td>${name}</td>
    //       <td>${contact}</td>
    //       <td>********</td>
    //     `;
    //     tableBody.appendChild(newRow);

    //     // Clear form and close modal
    //     document.getElementById('addUserForm').reset();
    //     const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
    //     modal.hide();
    //   } else {
    //     alert('Please fill in all fields');
    //   }
    // }
  </script>
</body>
</html>