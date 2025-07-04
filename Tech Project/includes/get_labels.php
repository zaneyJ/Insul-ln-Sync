<?php
session_start();
include_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM event_labels WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$labels = [];
while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row;
}

header('Content-Type: application/json');
echo json_encode($labels); 