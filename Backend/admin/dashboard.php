<?php
session_start();
require_once '../../db.php';

define("MAX_QUEUE_SIZE", 5); // Circular Queue Limit

// Protect admin access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

// ✅ DELETE logic
if (isset($_GET['delete_id'])) {
    $appointment_id = intval($_GET['delete_id']);

    // Delete from queue
    $stmt = $conn->prepare("DELETE FROM queue WHERE appointment_id = ?");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();

    // Delete from appointments
    $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}

// ✅ Handle walk-in registration
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patient_name = $_POST['patient_name'];
    $doctor_name = $_POST['doctor_name'];
    $department = $_POST['department'];

    // Step 1: Dummy user
    $full_name = $patient_name;
    $email = uniqid("walkin_") . "@walkin.com";
    $username = uniqid("walkin_");
    $password = password_hash("walkin123", PASSWORD_DEFAULT);

    $stmtUser = $conn->prepare("INSERT INTO users (full_name, email, username, password, role) VALUES (?, ?, ?, ?, 'user')");
    $stmtUser->bind_param("ssss", $full_name, $email, $username, $password);
    $stmtUser->execute();
    $user_id = $stmtUser->insert_id;

    // Step 2: Appointment
    $stmt = $conn->prepare("INSERT INTO appointments (doctor_name, department, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $doctor_name, $department, $user_id);
    $stmt->execute();
    $appointment_id = $stmt->insert_id;

    // Step 3: Circular token
    $used_tokens = [];
    $token_stmt = $conn->prepare("
        SELECT q.token_number 
        FROM queue q 
        JOIN appointments a ON q.appointment_id = a.id 
        WHERE a.department = ? AND DATE(a.created_at) = CURDATE()
    ");
    $token_stmt->bind_param("s", $department);
    $token_stmt->execute();
    $token_result = $token_stmt->get_result();
    while ($row = $token_result->fetch_assoc()) {
        $used_tokens[] = $row['token_number'];
    }

    $token_number = null;
    for ($i = 1; $i <= MAX_QUEUE_SIZE; $i++) {
        if (!in_array($i, $used_tokens)) {
            $token_number = $i;
            break;
        }
    }

    if ($token_number === null) {
        $_SESSION['error'] = "All 5 tokens are in use. Please wait.";
        header("Location: dashboard.php");
        exit();
    }

    // Step 4: Insert into queue
    $stmt = $conn->prepare("INSERT INTO queue (appointment_id, token_number) VALUES (?, ?)");
    $stmt->bind_param("ii", $appointment_id, $token_number);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}

// ✅ Load from database queue
$result = $conn->query("
    SELECT 
        a.id,
        a.doctor_name, 
        a.department, 
        a.created_at, 
        q.token_number 
    FROM appointments a
    JOIN queue q ON a.id = q.appointment_id
    WHERE DATE(a.created_at) = CURDATE()
    ORDER BY q.token_number ASC
");
$queue = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - MedQueue</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

  <header class="bg-blue-700 p-4 text-white flex justify-between items-center">
    <h1 class="text-2xl font-bold">🛠️ Admin Dashboard</h1>
    <a href="../../logout.php" class="text-red-200 hover:text-white">Logout</a>
  </header>

  <!-- Statistics Section -->
  <div class="container mx-auto p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
      <!-- Total Users Card -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-gray-600 font-semibold">Total Users</h3>
            <p class="text-3xl font-bold text-blue-600">
              <?php
              $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
              $stmt->execute();
              $result = $stmt->get_result();
              $row = $result->fetch_assoc();
              echo $row['total'];
              ?>
            </p>
          </div>
          <div class="text-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Total Appointments Card -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-gray-600 font-semibold">Total Appointments</h3>
            <p class="text-3xl font-bold text-green-600">
              <?php
              $stmt = $conn->prepare("SELECT COUNT(*) as total FROM appointments");
              $stmt->execute();
              $result = $stmt->get_result();
              $row = $result->fetch_assoc();
              echo $row['total'];
              ?>
            </p>
          </div>
          <div class="text-green-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Today's Appointments Card -->
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-gray-600 font-semibold">Today's Appointments</h3>
            <p class="text-3xl font-bold text-purple-600">
              <?php
              $stmt = $conn->prepare("SELECT COUNT(*) as total FROM appointments WHERE DATE(appointment_date) = CURDATE()");
              $stmt->execute();
              $result = $stmt->get_result();
              $row = $result->fetch_assoc();
              echo $row['total'];
              ?>
            </p>
          </div>
          <div class="text-purple-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Appointments Table -->
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-2xl font-bold mb-4">Appointments</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="bg-gray-50">
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointment Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php
            $stmt = $conn->prepare("SELECT a.*, u.full_name as patient_name, q.token_number 
                                  FROM appointments a 
                                  LEFT JOIN users u ON a.user_id = u.id 
                                  LEFT JOIN queue q ON a.id = q.appointment_id 
                                  ORDER BY a.appointment_date DESC");
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $status = $row['token_number'] ? 'In Queue' : 'Registered';
                $statusClass = $status === 'In Queue' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800';
                ?>
                <tr>
                  <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['patient_name'] ?? 'Walk-in Patient'); ?></td>
                  <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                  <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['department']); ?></td>
                  <td class="px-6 py-4 whitespace-nowrap"><?php echo date('Y-m-d', strtotime($row['appointment_date'])); ?></td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statusClass; ?>">
                      <?php echo $status; ?>
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <a href="dashboard.php?delete_id=<?php echo $row['id']; ?>" 
                       class="text-red-600 hover:text-red-800" 
                       onclick="return confirm('Are you sure you want to delete this appointment?')">
                      Delete
                    </a>
                  </td>
                </tr>
                <?php
              }
            } else {
              ?>
              <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No appointments found.</td>
              </tr>
              <?php
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
      <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
  <?php endif; ?>

  <main class="p-6 max-w-4xl mx-auto">
    <!-- Walk-in Form -->
    <section class="bg-white p-4 rounded shadow mb-6">
      <h2 class="text-xl font-bold mb-2">Register Walk-in Patient</h2>
      <form method="POST" class="space-y-4">
        <div>
          <label class="block">Patient Name</label>
          <input name="patient_name" required class="w-full border px-3 py-2 rounded">
        </div>
        <div>
          <label class="block">Doctor Name</label>
          <input name="doctor_name" required class="w-full border px-3 py-2 rounded">
        </div>
        <div>
          <label class="block">Department</label>
          <select name="department" required class="w-full border px-3 py-2 rounded">
            <option value="General">General</option>
            <option value="Cardiology">Cardiology</option>
            <option value="Orthopedics">Orthopedics</option>
            <option value="Pediatrics">Pediatrics</option>
          </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Register Patient</button>
      </form>
    </section>

    <!-- Queue Table -->
    <section class="bg-white p-4 rounded shadow">
      <h2 class="text-xl font-bold mb-4">Current Queue</h2>
      <?php if (count($queue) > 0): ?>
        <table class="min-w-full table-auto">
          <thead>
            <tr class="bg-gray-200">
              <th class="px-4 py-2">Token</th>
              <th class="px-4 py-2">Doctor</th>
              <th class="px-4 py-2">Department</th>
              <th class="px-4 py-2">Created</th>
              <th class="px-4 py-2">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($queue as $q): ?>
              <tr class="border-t">
                <td class="px-4 py-2 text-blue-600 font-bold"><?= $q['token_number'] ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($q['doctor_name']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($q['department']) ?></td>
                <td class="px-4 py-2"><?= date('d M Y, h:i A', strtotime($q['created_at'])) ?></td>
                <td class="px-4 py-2">
                  <a href="?delete_id=<?= $q['id'] ?>" onclick="return confirm('Remove patient from queue?')" class="text-red-600 hover:underline">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="text-gray-600">No patients in queue yet.</p>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
