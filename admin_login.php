<?php
require 'database/connection.php';
session_start();

if (isset($_SESSION['admin_id'])) {
    header("Location: admin/dashboard.php");
    exit;
}
$errorUsername = '';
$errorPassword = '';

if (isset($_POST['login'])) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

    // Validation
    if (empty($contact_no)) {
        $errorUsername = 'Username is required.';
    }
    if (empty($password)) {
        $errorPassword = 'Password is required.';
    }

    if (empty($errorContact_no) && empty($errorPassword)) {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['admin_id'] = $user['admin_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id'];  // should be 3 for driver
              
                switch($_SESSION['role_id']){
                    case 1: 
                        header("location: admin/dashboard.php");
                        break;
                    case 2:
                        header("location: admin/dashboard.php");
                        break;
                    case 3:
                        header("location: employee/home.php");
                         break;
                }
                exit;
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "User not found.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
       <link rel="stylesheet" href="assets/css/style.css">
</head>

<body style="background: #1f2937;">
    <div class="container min-vh-100 d-flex flex align-items-center justify-content-center">
        <div class="card shadow-lg p-4 w-100" style="max-width: 400px;">
            <h1 class="fs-3 text-center mb-3 text-secondary">Transportation Network Vehicle System</h1>
            <h2 class="text-center mb-4">ADMIN</h2>
            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                <div class="mb-3">
                    <label for="contact_no" class="form-label">Username:</label>
                    <input type="text" name="username" class="form-control  
                        <?php echo isset($_POST['login']) ? (empty($username) ? 'is-invalid' : null) : '' ?>"
                        placeholder="Enter your username">
                    <div class="invalid-feedback">
                        <span><?= $errorUsername ?></span>
                    </div>
                </div>


                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group position-relative">
                        <input type="password" id="password" name="password"
                            class="form-control <?php echo isset($_POST['login']) ? 
                            (empty($password) ? 'is-invalid' : '') : '' ?>"
                            placeholder="Enter your password">

                        <button type="button" class="btn rounded position-absolute end-0" id="togglePassword">
                            <img src="assets/image/hide.png" alt="hide" width="20" height="20">
                        </button>
                    </div>

                    <div class="invalid-feedback">
                        <span><?= $errorPassword ?></span>
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" name="login" class="btn btn-primary">Login</button>
                </div>
            
            </form>
        </div>

    </div>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>

</html>