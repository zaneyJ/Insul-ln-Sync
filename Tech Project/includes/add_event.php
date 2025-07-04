<?php
session_start();
include_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $start_datetime = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_datetime = mysqli_real_escape_string($conn, $_POST['end_time']);
    $label_id = isset($_POST['label_id']) ? (int)$_POST['label_id'] : null;

    $sql = "INSERT INTO calendar_events (user_id, title, description, start_datetime, end_datetime, label_id) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issssi", $user_id, $title, $description, $start_datetime, $end_datetime, $label_id);

    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
        $response['id'] = mysqli_insert_id($conn);
    }
}

header('Content-Type: application/json');
echo json_encode($response); 