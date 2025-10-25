<?php
require 'database/connection.php';
include 'includes/loader.php';

session_start();

if (isset($_SESSION['id'])) {
    header("Location: employee/home.php");
    exit;
}
$errorContact_no = '';
$errorPassword = '';

if (isset($_POST['login'])) {
    $contact_no = filter_input(INPUT_POST, 'contact_no', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

    // Validation
    if (empty($contact_no)) {
        $errorContact_no = 'Contact number is required.';
    }
    if (empty($password)) {
        $errorPassword = 'Password is required.';
    }

    if (empty($errorContact_no) && empty($errorPassword)) {
        $stmt = $conn->prepare("SELECT * FROM driver WHERE contact_no = ?");
        $stmt->bind_param("s", $contact_no);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['contact_no'] = $user['contact_no'];
                $_SESSION['role_id'] = $user['role_id'];  

                switch ($_SESSION['role_id']) {
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
                $errorPassword = "Incorrect password.";
            }
        } else {
            // echo "User not found.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - TNVS</title>
    <!-- Bootstrap & Font Awesome -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Styles -->
    <style>
        body {
            margin: 0;
            background: #1f2937;
            color: #ffffff;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(6px);
            max-width: 400px;
            width: 100%;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 24px;
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

        .input-group-text {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #4b5563;
            color: #d1d5db;
            border-radius: 8px 0 0 8px;
        }

        .toggle-password {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #4b5563;
            color: #d1d5db;
            border-left: none;
            border-radius: 0 8px 8px 0;
            padding: 0 12px;
            display: flex;
            align-items: center;
        }

        .btn-primary {
            background: #3b82f6;
            border: none;
            border-radius: 8px;
            padding: 10px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-primary:hover {
            background: #1e40af;
        }

        .btn-link {
            color: #3b82f6;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-link:hover {
            color: #1e40af;
            text-decoration: underline;
        }

        .alert {
            background: rgba(255, 255, 255, 0.1);
            color: #dc2626;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            padding: 10px;
        }

        .invalid-feedback {
            font-size: 0.85rem;
        }

        @media (max-width: 475px) {
            .card {
                padding: 24px;
            }

            .card-title {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <h1 class="card-title">Login</h1>
        <?php if (!empty($errorGeneral)): ?>
            <div class="alert"><?php echo htmlspecialchars($errorGeneral); ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="contact_no" class="form-label">Contact Number</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input type="text"
                        id="contact_no"
                        name="contact_no"
                        placeholder="Enter contact number"
                        class="form-control <?php echo !empty($errorContact_no) ? 'is-invalid' : ''; ?>"
                        required>
                    <div class="invalid-feedback">
                        <?php echo htmlspecialchars($errorContact_no ?? ''); ?>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password"
                        id="password"
                        name="password"
                        placeholder="Enter password"
                        class="form-control <?php echo !empty($errorPassword) ? 'is-invalid' : ''; ?>"
                        required>
                    <button type="button" class="toggle-password" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div class="invalid-feedback">
                        <?php echo htmlspecialchars($errorPassword ?? ''); ?>
                    </div>
                </div>
            </div>
            <button type="submit" name="login" class="btn btn-primary">Login</button>
            <div class="text-center mt-3">
                <a href="register.php" class="btn-link">Don't have an account? Register</a>
            </div>
        </form>
    </div>


    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
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


        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';

            password.setAttribute('type', type);
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>