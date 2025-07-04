<?php

// Start a new session or resume the existing one
session_start();
// Destroy any existing session data to ensure a fresh login
session_destroy();

// Include the database configuration file for DB connection
include_once 'config.php';

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize the username input to prevent SQL injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    // Get the account type (patient or care_giver) from the form
    $acctype = $_POST["acctype"];
    // Get the password from the form
    $pwd = $_POST["pwd"];

    // Build the SQL query based on account type
    if ($acctype == 'patient') {
        // Query the patients table for the username
        $sql = "SELECT * FROM patients WHERE username = '$username'";
    } else if ($acctype == 'care_giver') {
        // Query the care_givers table for the username
        $sql = "SELECT * FROM care_givers WHERE username = '$username'";
    }

    // Execute the query
    $login = mysqli_query($conn, $sql);

    // Check if a user with the given username exists
    if (mysqli_num_rows($login) === 1) {
        $row = mysqli_fetch_assoc($login);

        // Verify the password using PHP's password_verify function
        if (password_verify($pwd, $row['pwd'])) {

            // Set session variables for the logged-in user
            $_SESSION['username'] = $username;
            $_SESSION['acctype'] = $acctype;
            
            // Store the user's ID in the session based on account type
            if ($acctype == 'patient') {
                $_SESSION['patient_id'] = $row['id'];
            } else if ($acctype == 'care_giver') {
                $_SESSION['caregiver_id'] = $row['id'];
            }

            // Redirect to the main dashboard after successful login
            header("Location: ../index1.php?message=Log in successful!");

            exit();

        } else {
            // If the password is incorrect, show an error message
            echo "Invalid username or password.";
        }
    } else {
        // If the username does not exist, show an error message
        echo "Invalid username or password.";
    }
}
?>
