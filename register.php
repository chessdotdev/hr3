<?php
require 'database/connection.php';

if (isset($_POST['register'])) {
    $firstname    = trim($_POST['firstname']);
    $lastname     = trim($_POST['lastname']);
    $middlename   = trim($_POST['middlename']);
    $contact_no   = trim($_POST['contact_no']);
    $email        = trim($_POST['email']);
    $plate_number = trim($_POST['plate_number']);
    $vehicle_type = trim($_POST['vehicle_type']);
    $password     = $_POST['password'];
    $role_id      = 3; // Driver

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    if (empty($firstname) || empty($lastname) || empty($contact_no) || empty($email) || empty($password)) {
        echo "Please fill up all required fields.";
    } else {
        // Check for duplicates
        $stmt = $conn->prepare("SELECT id FROM driver WHERE contact_no = ? OR email = ?");
        $stmt->bind_param("ss", $contact_no, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "This contact number or email is already registered.";
        } else {
            // Insert new driver
            $stmt = $conn->prepare("INSERT INTO driver 
            (firstname, 
            lastname,
            middlename, 
            contact_no, 
            email, 
            plate_number,
            vehicle_type, 
            password, 
            role_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "ssssssssi",
                $firstname,
                $lastname,
                $middlename,
                $contact_no,
                $email,
                $plate_number,
                $vehicle_type,
                $password_hashed,
                $role_id
            );

            if ($stmt->execute()) {
                //  Get the new driver's ID after successful insert
                $driver_id = $conn->insert_id;

                //  Insert a blank schedule row with NULL values
                $sql_schedule = "INSERT INTO schedule (driver_id, shift_date, shift_start, shift_end) VALUES (?, NULL, NULL, NULL)";
                $stmt2 = $conn->prepare($sql_schedule);
                $stmt2->bind_param("i", $driver_id);
                $stmt2->execute();

                echo "Driver registered successfully and added to schedule list!";
            } else {
                echo "Error inserting driver: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Registration</title>
</head>
<body>
    <form action="register.php" method="POST">
        <label>First Name</label>
        <input type="text" name="firstname" required><br>

        <label>Last Name</label>
        <input type="text" name="lastname" required><br>

        <label>Middle Name</label>
        <input type="text" name="middlename"><br>

        <label>Contact No</label>
        <input type="text" name="contact_no" required><br>

        <label>Email</label>
        <input type="email" name="email" required><br>

        <label>Plate number</label>
        <input type="text" name="plate_number" required><br>

        <label>Vehicle Type</label>
        <input type="text" name="vehicle_type" required><br>

        <label>Password</label>
        <input type="password" name="password" required><br>

        <input type="submit" name="register" value="Register">
    </form>
</body>
</html>

