<?php

include_once 'includes/config.php';
require_once 'includes/encryption.php';
require_once 'includes/encryption_config.php';

// Check if user is logged in and is a patient
if (!isset($_SESSION['username']) || !isset($_SESSION['patient_id'])) {
    header("Location: login.php?error=Please login as a patient to access this page");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blood Cholesterol Log</title>

<link rel="stylesheet" href="style.css">

  </head>

  <body>

<?php include 'includes/navbar.php'; ?>

<?php
if (isset($_SESSION['username'])) {
    echo "<h2>Welcome " . $_SESSION['username'] . "!</h2>";
} 


if (isset($_GET['total_cholesterol'])) {
    echo "your total cholesterol level is " . $_GET['total_cholesterol'];
}

?>
    
<form method="post" action="includes/cholesterol.php">

<fieldset>
  <legend><h3>Blood Cholesterol Log</h3></legend>
  <table width="100%" cellpadding="10">
    <tr align="center">
    <td width="25%">
        <br>
    <label for="HDL" required>Enter your HDL cholesterol level: </label><br><br>
            <input type="number" step="0.01" min="0" name="HDL" required>
        </td>
        <td width="25%">
        <br>
    <label for="LDL" required>Enter your LDL cholesterol level: </label><br><br>
            <input type="number" step="0.01" min="0" name="LDL" required>
        </td>
        <td width="25%">
        <br>
    <label for="triglycerides" required>Enter your triglycerides level: </label><br><br>
            <input type="number" step="0.01" min="0" name="triglycerides" required>
        </td>
        </td>
              <td width="25">
                <div style=" margin-top: 20px; margin-left: 50px; margin-right: 20px;">
                <label for="time">Time:</label><br /><br /><br>
                <input required type="datetime-local" name="time" />
              </td></div>
    </tr>
    <tr>
<td colspan="4" style="text-align: center; padding-top: 20px;">
<button name="submit" type="submit">Submit</button>
</td>
</tr>
  </table>
</fieldset>

</form>

<fieldset>
  <legend><h3>Blood Cholesterol Tracker</h3></legend>
  <table width="100%" cellpadding="10">
  <tr>
        <th>HDL</th>
        <th>LDL</th>
        <th>Triglycerides</th>
        <th>Total Cholesterol</th>
        <th>Date and Time</th>
        <th>Actions</th>
    </tr>

<?php
// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM patient_cholesterol WHERE patient_id = ?");
$stmt->bind_param("i", $_SESSION['patient_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Decrypt each value
        $decryptedHDL = decryptData($row['hdl']);
        $decryptedLDL = decryptData($row['ldl']);
        $decryptedTriglycerides = decryptData($row['triglycerides']);
        $decryptedTotal = decryptData($row['total_cholesterol']);

        echo "<tr>
            <td>{$decryptedHDL}</td>
            <td>{$decryptedLDL}</td>
            <td>{$decryptedTriglycerides}</td>
            <td>{$decryptedTotal}</td>
            <td>{$row['time']}</td>            
            <td>
                <a href='includes/Edit-Delete/edit_customers.php?id={$row['id']}' onclick=\"return confirm('Are you sure you want to edit this record?');\">Edit</a> | 
                <a href='includes/Edit-Delete/delete_customers.php?id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No records found. Start adding your cholesterol readings above.</td></tr>";
}
$stmt->close();
?>
  </table>
</fieldset>

</body>
</html>


