<?php
require_once __DIR__ . '/../db.php';

// Clear doctors table
$sql = "DELETE FROM doctors";
if ($conn->query($sql) === TRUE) {
    echo "Doctors table cleared successfully";
} else {
    echo "Error clearing doctors table: " . $conn->error;
}
$conn->close();
?>
