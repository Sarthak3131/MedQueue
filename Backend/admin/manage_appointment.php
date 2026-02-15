<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
require '../db.php';

// Handle delete
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM appointments WHERE id = $delete_id");
    header("Location: manage_appointments.php");
    exit();
}

// Fetch all appointments
$result = $conn->query("SELECT a.*, u.full_name FROM appointments a JOIN users u ON a.user_id = u.id");
$appointments = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Manage Appointments - Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
  <h1 class="text-3xl font-bold text-blue-800 mb-4">🗂 Manage Appointments</h1>
  <a href="dashboard.php" class="text-blue-600 underline mr-4">← Back to Dashboard</a>
  <a href="logout.php" class="text-red-600 underline">Logout</a>

  <div class="mt-6 bg-white p-4 rounded shadow">
    <table class="w-full table-auto">
      <thead class="bg-blue-100">
        <tr>
          <th class="px-3 py-2">Patient</th>
          <th class="px-3 py-2">Department</th>
          <th class="px-3 py-2">Doctor</th>
          <th class="px-3 py-2">Date</th>
          <th class="px-3 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($appointments as $appt): ?>
        <tr class="border-b hover:bg-gray-50">
          <td class="px-3 py-2"><?= htmlspecialchars($appt['full_name']) ?></td>
          <td class="px-3 py-2"><?= htmlspecialchars($appt['department']) ?></td>
          <td class="px-3 py-2"><?= htmlspecialchars($appt['doctor_name']) ?></td>
          <td class="px-3 py-2"><?= htmlspecialchars($appt['created_at']) ?></td>
          <td class="px-3 py-2">
            <a href="?delete_id=<?= $appt['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Delete this appointment?')">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
