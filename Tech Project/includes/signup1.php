<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize form data
    $acctype = mysqli_real_escape_string($conn, $_POST['acctype']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $pwd = password_hash($_POST['pwd'], PASSWORD_DEFAULT); // Hash the password
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $insulin = mysqli_real_escape_string($conn, $_POST['insulin']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $height = mysqli_real_escape_string($conn, $_POST['height']);
    $weight = mysqli_real_escape_string($conn, $_POST['weight']);
    $conditions = mysqli_real_escape_string($conn, $_POST['conditions']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $em_name = mysqli_real_escape_string($conn, $_POST['em_name']);
    $em_address = mysqli_real_escape_string($conn, $_POST['em_address']);
    $em_phone = mysqli_real_escape_string($conn, $_POST['em_phone']);

    // Check if username exists in patients or care_givers
    $check_sql = "SELECT id FROM patients WHERE username = ? UNION SELECT id FROM care_givers WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $username, $username);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        // Username already exists
        header("Location: ../signup.php?error=Username already taken. Please choose another.");
        exit();
    }
    $check_stmt->close();

    // Determine which table to insert into
    $table = ($acctype == 'patient') ? 'patients' : 'care_givers';

    // Prepare the SQL statement
    $sql = "INSERT INTO $table (acctype, username, pwd, firstname, lastname, dob, gender, insulin, type, height, weight, conditions, address, email, phone, em_name, em_address, em_phone) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssssddsssssss", 
        $acctype, $username, $pwd, $firstname, $lastname, $dob, $gender, $insulin, $type, 
        $height, $weight, $conditions, $address, $email, $phone, $em_name, $em_address, $em_phone);

    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        // Success - redirect to login page with success message
        header("Location: ../login.php?success=Account created successfully");
        exit();
    } else {
        // Error - redirect back to signup with error message
        header("Location: ../signup.php?error=" . urlencode(mysqli_error($conn)));
        exit();
    }
} else {
    // If not POST request, redirect to signup page
    header("Location: ../signup.php");
    exit();
} 