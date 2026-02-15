<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "hospital_queue";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $password, $dbname);
    $conn->set_charset("utf8mb4"); 
} catch (Exception $e) {
    error_log($e->getMessage());
    exit('Database connection error. Please check your connection.');
}
?>
