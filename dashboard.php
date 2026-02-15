<?php
session_start();
require 'db.php';

$user = $_SESSION['user'];
if (is_string($user)) {
    $user = json_decode($user, true);
    $_SESSION['user'] = $user;
}

if (!isset($user['id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $conn->prepare("
    SELECT 
        a.doctor_name, 
        a.department, 
        a.created_at, 
        q.token_number
    FROM appointments a
    LEFT JOIN queue q ON a.id = q.appointment_id
    WHERE a.user_id = ?
    ORDER BY a.created_at DESC
");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard - MedQueue</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="images/d1.png" />
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen font-sans text-gray-800">

  <!-- Navbar -->
  <header class="bg-blue-600 text-white p-4 shadow-md">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <img src="images/d1.png" class="w-8 h-8" alt="Logo" />
        <h1 class="text-xl font-bold">MedQueue</h1>
      </div>

      <nav class="flex items-center space-x-6 text-sm md:text-base">
        <a href="index.php" class="hover:underline">Home</a>
        <a href="doctor.php" class="hover:underline">Doctors</a>
        <a href="about.php" class="hover:underline">About</a>
        <a href="feedback.php" class="hover:underline">Feedback</a>

        <?php if ($user): ?>
          <a href="dashboard.php" class="hover:underline">Dashboard</a>
          <a href="logout.php" class="hover:underline text-red-200">Logout</a>
        <?php else: ?>
          <a href="login.php" class="hover:underline">Login</a>
          <a href="register.php" class="hover:underline">Register</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <!-- Welcome -->
  <section class="py-10 px-4 text-center">
    <h2 class="text-3xl font-extrabold text-blue-800 mb-2">👋 Welcome, <?= htmlspecialchars($user['full_name'] ?? $user['email']) ?>!</h2>
    <p class="text-gray-600 text-lg">Below are your appointment details and token numbers.</p>
  </section>

  <!-- Appointments Table -->
  <main class="max-w-6xl mx-auto px-6 py-8 bg-white shadow-lg rounded-2xl">
    <?php if (count($appointments) > 0): ?>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto border-collapse rounded-lg overflow-hidden">
          <thead>
            <tr class="bg-blue-100 text-blue-800">
              <th class="px-6 py-3 text-left font-semibold">Doctor</th>
              <th class="px-6 py-3 text-left font-semibold">Department</th>
              <th class="px-6 py-3 text-left font-semibold">Token</th>
              <th class="px-6 py-3 text-left font-semibold">Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($appointments as $appt): ?>
              <tr class="border-b hover:bg-blue-50 transition duration-200">
                <td class="px-6 py-4"><?= htmlspecialchars($appt['doctor_name']) ?></td>
                <td class="px-6 py-4"><?= htmlspecialchars($appt['department']) ?></td>
                <td class="px-6 py-4 text-blue-700 font-bold"><?= $appt['token_number'] ?? '—' ?></td>
                <td class="px-6 py-4"><?= htmlspecialchars(date("M d, Y", strtotime($appt['created_at']))) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-gray-600 text-center text-lg mt-4">You haven't booked any appointments yet.</p>
    <?php endif; ?>
  </main>

  <!-- Footer -->
  <footer class="mt-10 py-4 text-center text-sm text-gray-600">
    &copy; <?= date('Y') ?> MedQueue. All rights reserved.
  </footer>

</body>
</html>
