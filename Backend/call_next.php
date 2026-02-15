<?php
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor = $_POST['doctor'] ?? '';
    $department = $_POST['department'] ?? '';

    // Get the earliest appointment + token for the given doctor
    $stmt = $conn->prepare("
        SELECT q.id AS queue_id, q.token_number, a.doctor_name 
        FROM queue q
        JOIN appointments a ON q.appointment_id = a.id
        WHERE a.doctor_name = ? AND a.department = ?
        ORDER BY q.timestamp ASC
        LIMIT 1
    ");
    $stmt->bind_param("ss", $doctor, $department);
    $stmt->execute();
    $result = $stmt->get_result();
    $entry = $result->fetch_assoc();

    if ($entry) {
        // Delete the called token
        $del = $conn->prepare("DELETE FROM queue WHERE id = ?");
        $del->bind_param("i", $entry['queue_id']);
        $del->execute();

        echo json_encode([
            "success" => true,
            "message" => "✅ Token #{$entry['token_number']} for {$entry['doctor_name']} called!"
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "❌ No tokens in queue."]);
    }
}
?>
