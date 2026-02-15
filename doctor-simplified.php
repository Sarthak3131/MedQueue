<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['user'];
require_once 'db.php';

// Hardcoded doctors data
$doctors = [
    [
        'id' => 1,
        'name' => 'Dr. John Smith',
        'department' => 'General Medicine'
    ],
    [
        'id' => 2,
        'name' => 'Dr. Sarah Johnson',
        'department' => 'Cardiology'
    ],
    [
        'id' => 3,
        'name' => 'Dr. Michael Brown',
        'department' => 'Diagnostics'
    ],
    [
        'id' => 4,
        'name' => 'Dr. Emily Wilson',
        'department' => 'Pediatrics'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Doctors - MedQueue</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script>
    // Global variables
    let currentDoctor = null;
    let selectedDate = null;

    function bookAppointment(doctorId, doctorName, department) {
      currentDoctor = {
        id: parseInt(doctorId),
        name: doctorName,
        department: department
      };

      // Show date picker modal
      const datePickerModal = document.getElementById('datePickerModal');
      if (!datePickerModal) {
        console.error('Date picker modal not found');
        return;
      }

      datePickerModal.style.display = 'flex';
      
      // Initialize flatpickr if not already initialized
      if (!document.getElementById('appointmentDate')._flatpickr) {
        flatpickr("#appointmentDate", {
          minDate: "today",
          dateFormat: "Y-m-d",
          onChange: function(selectedDates, dateStr) {
            selectedDate = dateStr;
          }
        });
      }
    }

    function confirmDate() {
      if (!selectedDate) {
        alert('Please select a date');
        return;
      }

      processPayment(currentDoctor.id, currentDoctor.department, selectedDate);
    }

    function processPayment(doctorId, department, appointmentDate) {
      // Create a form to submit
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'Backend/book.php';
      form.style.display = 'none';

      // Add form fields
      const fields = [
        { name: 'doctor_name', value: currentDoctor.name },
        { name: 'department', value: department },
        { name: 'appointment_date', value: appointmentDate }
      ];

      fields.forEach(field => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = field.name;
        input.value = field.value;
        form.appendChild(input);
      });

      document.body.appendChild(form);
      form.submit();
    }

    function closeDatePicker() {
      document.getElementById('datePickerModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const datePicker = document.getElementById('datePickerModal');
      if (event.target == datePicker) {
        datePicker.style.display = 'none';
      }
    }
  </script>
</head>
<body class="bg-gradient-to-b from-blue-50 via-white to-blue-100 min-h-screen text-gray-800 font-sans">
  <!-- Navbar -->
  <header class="bg-blue-600 text-white p-4 shadow-md">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <h1 class="text-xl font-bold">MedQueue</h1>
        <p class="text-sm">Welcome, <?php echo htmlspecialchars($user['name']); ?></p>
      </div>
      <nav class="flex space-x-4">
        <a href="index.php" class="text-white hover:text-blue-200">Home</a>
        <a href="doctor.php" class="text-white hover:text-blue-200">Doctors</a>
        <a href="appointment.php" class="text-white hover:text-blue-200">Appointments</a>
        <a href="feedback.php" class="text-white hover:text-blue-200">Feedback</a>
        <a href="Backend/logout.php" class="text-white hover:text-blue-200">Logout</a>
      </nav>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8 text-blue-800">Available Doctors</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php foreach ($doctors as $doctor): ?>
        <div class="doctor-card bg-white rounded-lg shadow-md p-6 text-center cursor-pointer hover:shadow-lg transition-shadow" onclick="bookAppointment('<?php echo htmlspecialchars($doctor['id']); ?>', '<?php echo htmlspecialchars($doctor['name']); ?>', '<?php echo htmlspecialchars($doctor['department']); ?>')" data-dept="<?php echo htmlspecialchars($doctor['department']); ?>">
          <div class="flex flex-col items-center space-y-4">
            <img src="images/doctor-placeholder.jpg" alt="<?php echo htmlspecialchars($doctor['name']); ?>" class="w-32 h-32 rounded-full object-cover">
            <h3 class="text-xl font-bold text-blue-600"><?php echo htmlspecialchars($doctor['name']); ?></h3>
            <p class="text-gray-600"><?php echo htmlspecialchars($doctor['department']); ?></p>
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
              Book Appointment
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </main>

  <!-- Date Picker -->
  <div id="datePickerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
      <div class="space-y-4">
        <h2 class="text-2xl font-bold mb-4">Select Appointment Date</h2>
        <div class="relative">
          <input type="text" id="appointmentDate" class="w-full border p-2 rounded" placeholder="Select date">
        </div>
        <div class="flex justify-end space-x-2">
          <button onclick="closeDatePicker()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
          <button onclick="confirmDate()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Confirm</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Load flatpickr -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>
</html>
