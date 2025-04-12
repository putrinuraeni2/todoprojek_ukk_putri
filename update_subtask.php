<?php
include 'config.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $subtask_id = $_GET['id'];
    $status = $_GET['status'];

    // Update status subtugas
    $query = "UPDATE subtasks SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $subtask_id);
    $success = $stmt->execute();

    echo json_encode(["success" => $success]);
} else {
    echo json_encode(["success" => false]);
}
?>
