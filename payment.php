<?php
session_start();

if (!isset($_SESSION['appointment_id']) || !isset($_SESSION['fee'])) {
    header("Location: dashboard.php");
    exit();
}

$appointment_id = $_SESSION['appointment_id'];
$fee = $_SESSION['fee'];
$success_msg = $_SESSION['success'] ?? "Appointment booked successfully!";
unset($_SESSION['appointment_id'], $_SESSION['fee'], $_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment | MedQueue</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-2xl shadow-lg max-w-md text-center">
    <h2 class="text-2xl font-bold text-green-600 mb-4">🎉 <?php echo $success_msg; ?></h2>
    <p class="text-lg mb-2">Appointment ID: <strong><?php echo $appointment_id; ?></strong></p>
    <p class="text-lg mb-4">Please pay ₹<?php echo $fee; ?> using the QR code below:</p>

    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=upi://pay?pa=yourupi@bank&pn=MedQueue&am=<?php echo $fee; ?>&cu=INR" 
         alt="Pay ₹<?php echo $fee; ?>" class="mx-auto mb-4 rounded shadow">

    <a href="dashboard.php" class="inline-block px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
      Back to Dashboard
    </a>
  </div>
</body>
</html>
