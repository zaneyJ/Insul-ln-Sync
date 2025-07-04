<?php

include_once 'config.php';

session_unset();

if (session_destroy()) {
    header("Location: ../index1.php?message=You have successfully logged out.");
    exit();
} else {
    header("Location: ../index1.php?message=Error logging out. Please try again.");
    exit();
}

?>
