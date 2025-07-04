<?php

include_once 'includes/config.php';

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insul-In-Sync Log In Page</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/accessibility.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>
<?php include 'includes/accessibility_widget.php'; ?>

<div class="container">
<?php
if (isset($_SESSION['username'])) {
    echo "<h2>Welcome " . $_SESSION['username'] . "!</h2>";
} 
?>
        <form method="post"  action="includes/login.php">
        <fieldset>
          <legend><h3>Log In</h3></legend>
          <table width="100%" cellpadding="10">
            <tr align="center">
            <td width="33.3%">
  <label for="acctype" style="display: block; margin-bottom: 20px; margin-top: 20px;">Account type:</label>
  <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 20px;">
    <label for="patient">
      <input type="radio" id="patient" name="acctype" value="patient"  />
      Patient
    </label>
    
    <label for="care_giver">
      <input type="radio" id="care_giver" name="acctype" value="care_giver" />
      Care Giver
    </label>
  </div>
</td>
            <td width="33.3%"><label for="username" required>Enter your username: </label><br><br>
                    <input type="text" name="username">
                </td>
                <td width="33.3%"><label for="pwd" required>Enter your password: </label><br><br>
                    <input type="password" name="pwd">
                </td>
            </tr>
            <tr>
  <td colspan="3" style="text-align: center; padding-top: 20px;">
    <button name="submit" type="submit">Submit</button>
  </td>
</tr>
          </table>
        </fieldset>
    </form>
</div>
<script src="js/accessibility.js"></script>
</body>
</html>
