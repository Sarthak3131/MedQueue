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
  <link rel="stylesheet" href="https://npmcdn.com/flatpickr@4.6.13/dist/themes/material_blue.css">

  <!-- Load flatpickr first -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script>
    // Global variables
    let currentDoctor = null;
    let selectedDate = null;

    // Book Appointment with Payment
    function bookAppointment(doctorId, doctorName, department, fee) {
      currentDoctor = {
        id: parseInt(doctorId),
        name: doctorName,
        department: department,
        fee: parseFloat(fee)
      };

      // Show date picker modal
      const datePickerModal = document.getElementById('datePickerModal');
      if (!datePickerModal) {
        console.error('Date picker modal not found');
        return;
      }

      datePickerModal.style.display = 'flex';
      
      // Initialize flatpickr
      try {
        flatpickr("#appointmentDate", {
          minDate: "today",
          dateFormat: "Y-m-d",
          disable: [
            function(date) {
              // Disable weekends
              return (date.getDay() === 0 || date.getDay() === 6);
            },
            // Disable past dates
            function(date) {
              return date.getTime() < Date.now();
            }
          ],
          onChange: function(selectedDates, dateStr) {
            selectedDate = dateStr;
          }
        });
      } catch (e) {
        console.error('Error initializing flatpickr:', e);
      }
    }

    function confirmDate() {
      if (!selectedDate) {
        alert('Please select a date');
        return;
      }

      const paymentModal = document.getElementById('paymentModal');
      const modalContent = paymentModal.querySelector('.modal-content');
      
      // Update modal content with doctor details and fee
      modalContent.innerHTML = `
        <h2 class="text-2xl font-bold mb-4">Payment Details</h2>
        <div class="space-y-4">
          <p><strong>Doctor:</strong> ${currentDoctor.name}</p>
          <p><strong>Department:</strong> ${currentDoctor.department}</p>
          <p><strong>Fee:</strong> ₹${currentDoctor.fee}</p>
          <p><strong>Appointment Date:</strong> ${selectedDate}</p>
          <div class="space-y-2">
            <label class="block">Payment Method:</label>
            <select id="paymentMethod" class="w-full border p-2 rounded">
              <option value="online">Online Payment</option>
              <option value="cash">Cash at Hospital</option>
            </select>
          </div>
          <button onclick="processPayment(${currentDoctor.id}, '${currentDoctor.department}', '${selectedDate}', '${currentDoctor.fee}')" 
                  class="w-full bg-green-600 text-white p-2 rounded hover:bg-green-700">
            Confirm Payment
          </button>
        </div>
      `;

      // Show payment modal and hide date picker
      paymentModal.style.display = 'flex';
      document.getElementById('datePickerModal').style.display = 'none';
    }

    function processPayment(doctorId, department, appointmentDate, fee) {
      const paymentMethod = document.getElementById('paymentMethod').value;
      
      // Create a form to submit
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'Backend/book.php';
      form.style.display = 'none';

      // Add form fields
      form.innerHTML = `
        <input type="hidden" name="doctor_id" value="${doctorId}">
        <input type="hidden" name="doctor_name" value="${currentDoctor.name}">
        <input type="hidden" name="department" value="${department}">
        <input type="hidden" name="appointment_date" value="${appointmentDate}">
        <input type="hidden" name="fee" value="${fee}">
        <input type="hidden" name="payment_method" value="${paymentMethod}">
      `;

      document.body.appendChild(form);
      form.submit();
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('paymentModal');
      if (event.target == modal) {
        modal.style.display = 'none';
      }

      const datePicker = document.getElementById('datePickerModal');
      if (event.target == datePicker) {
        datePicker.style.display = 'none';
      }
    }

    // Initialize flatpickr globally
    document.addEventListener('DOMContentLoaded', function() {
      try {
        flatpickr("#appointmentDate", {
          minDate: "today",
          dateFormat: "Y-m-d",
          disable: [
            function(date) {
              // Disable weekends
              return (date.getDay() === 0 || date.getDay() === 6);
            },
            // Disable past dates
            function(date) {
              return date.getTime() < Date.now();
            }
          ],
          onChange: function(selectedDates, dateStr) {
            selectedDate = dateStr;
          }
        });
      } catch (e) {
        console.error('Error initializing flatpickr:', e);
      }
    });
  </script>
  <link rel="icon" href="images/d1.png" />
</head>
<body class="bg-gradient-to-b from-blue-50 via-white to-blue-100 min-h-screen text-gray-800 font-sans">

  <!-- Navbar -->
  <header class="bg-blue-600 text-white p-4 shadow-md">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <img src="images/d1.png" class="w-8 h-8" alt="Logo" />
        <h1 class="text-xl font-bold">MedQueue</h1>
      </div>

      <nav class="flex items-center space-x-6 text-sm md:text-base">
        <a href="index.php" class="hover:underline">Home</a>
        <a href="doctor.php" class="underline">Doctors</a>
        <a href="about.php" class="hover:underline">About</a>
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

  <!-- Filters -->
  <div class="p-4 text-center space-x-2">
    <button onclick="filterDoctors('all')" class="px-4 py-2 rounded bg-gradient-to-r from-gray-300 to-gray-400 hover:from-gray-400 hover:to-gray-300 text-gray-800 font-semibold">All</button>
    <button onclick="filterDoctors('General')" class="px-4 py-2 rounded bg-gradient-to-r from-green-400 to-green-500 hover:from-green-500 hover:to-green-400 text-white font-semibold">General</button>
    <button onclick="filterDoctors('Cardiology')" class="px-4 py-2 rounded bg-gradient-to-r from-blue-400 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white font-semibold">Cardiology</button>
    <button onclick="filterDoctors('Diagnostics')" class="px-4 py-2 rounded bg-gradient-to-r from-purple-400 to-purple-500 hover:from-purple-500 hover:to-purple-400 text-white font-semibold">Diagnostics</button>
  </div>

  <!-- Doctor Cards -->
  <main class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($doctors as $doctor): ?>
        <div class="doctor-card bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow duration-300" data-dept="<?php echo htmlspecialchars($doctor['department']); ?>">
          <div class="flex flex-col items-center space-y-4">
            <img src="images/doctor-placeholder.jpg" alt="<?php echo htmlspecialchars($doctor['name']); ?>" class="w-32 h-32 rounded-full object-cover">
            <h3 class="text-xl font-bold text-blue-600"><?php echo htmlspecialchars($doctor['name']); ?></h3>
            <p class="text-gray-600"><?php echo htmlspecialchars($doctor['department']); ?></p>
            <button onclick="bookAppointment('<?php echo htmlspecialchars($doctor['id']); ?>', '<?php echo htmlspecialchars($doctor['name']); ?>', '<?php echo htmlspecialchars($doctor['department']); ?>')" 
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
              Book Appointment
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </main>

  <!-- Modal -->
  <div id="booking-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <form method="POST" action="Backend/book.php" class="bg-white p-6 rounded-xl shadow-xl space-y-4 w-full max-w-md animate-fadeIn">
      <h2 class="text-xl font-bold text-center text-blue-800">Book Appointment</h2>
      <input type="hidden" name="doctor_name" id="doctor-name-input">
      <input type="hidden" name="department" id="department-input">

  <!-- Payment Modal -->
  <div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
      <div class="modal-content"></div>
    </div>
  </div>

  <!-- Date Picker -->
  <div id="datePickerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
      <div class="space-y-4">
        <h2 class="text-2xl font-bold mb-4">Select Appointment Date</h2>
        <div class="relative">
          <input type="text" id="appointmentDate" class="w-full border p-2 rounded" placeholder="Select date">
        </div>
  </div>
  </div>
        <div class="flex justify-end space-x-2">
          <button onclick="closeDatePicker()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
          <button onclick="confirmDate()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Confirm</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Script -->
  <script>
    function filterDoctors(dept) {
      const cards = document.querySelectorAll('.doctor-card');
      cards.forEach(card => {
        if (dept === 'all' || card.getAttribute('data-dept') === dept) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    }

    // Initial filter
    filterDoctors('all');

    const selectedDate = null;

    // Store doctor details globally
    let currentDoctor = null;
    let selectedDate = null;

    // Book Appointment with Payment
    function bookAppointment(doctorId, doctorName, department, fee) {
      currentDoctor = {
        id: parseInt(doctorId),
        name: doctorName,
        department: department,
        fee: parseFloat(fee)
      };

      // Show date picker modal
      const datePickerModal = document.getElementById('datePickerModal');
      if (!datePickerModal) {
        console.error('Date picker modal not found');
        return;
      }

      datePickerModal.style.display = 'flex';
      
      // Initialize flatpickr
      try {
        flatpickr("#appointmentDate", {
          minDate: "today",
          dateFormat: "Y-m-d",
          disable: [
            function(date) {
              // Disable weekends
              return (date.getDay() === 0 || date.getDay() === 6);
            },
            // Disable past dates
            function(date) {
              return date.getTime() < Date.now();
            }
          ],
          onChange: function(selectedDates, dateStr) {
            selectedDate = dateStr;
          }
        });
      } catch (e) {
        console.error('Error initializing flatpickr:', e);
      }
    }

    function confirmDate() {
      if (!selectedDate) {
        alert('Please select a date');
        return;
      }

      const paymentModal = document.getElementById('paymentModal');
      const modalContent = paymentModal.querySelector('.modal-content');
      
      // Update modal content with doctor details and fee
      modalContent.innerHTML = `
        <h2 class="text-2xl font-bold mb-4">Payment Details</h2>
        <div class="space-y-4">
          <p><strong>Doctor:</strong> ${currentDoctor.name}</p>
          <p><strong>Department:</strong> ${currentDoctor.department}</p>
          <p><strong>Fee:</strong> ₹${currentDoctor.fee}</p>
          <p><strong>Appointment Date:</strong> ${selectedDate}</p>
          <div class="space-y-2">
            <label class="block">Payment Method:</label>
            <select id="paymentMethod" class="w-full border p-2 rounded">
              <option value="online">Online Payment</option>
              <option value="cash">Cash at Hospital</option>
            </select>
          </div>
          <button onclick="processPayment(${currentDoctor.id}, '${currentDoctor.department}', '${selectedDate}', '${currentDoctor.fee}')" 
                  class="w-full bg-green-600 text-white p-2 rounded hover:bg-green-700">
            Confirm Payment
          </button>
        </div>
      `;

      // Show payment modal and hide date picker
      paymentModal.style.display = 'flex';
      document.getElementById('datePickerModal').style.display = 'none';
    }

    function processPayment(doctorId, department, appointmentDate, fee) {
      const paymentMethod = document.getElementById('paymentMethod').value;
      
      // Create a form to submit
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'Backend/book.php';
      form.style.display = 'none';

      // Add form fields
      form.innerHTML = `
        <input type="hidden" name="doctor_id" value="${doctorId}">
        <input type="hidden" name="doctor_name" value="${currentDoctor.name}">
        <input type="hidden" name="department" value="${department}">
        <input type="hidden" name="appointment_date" value="${appointmentDate}">
        <input type="hidden" name="fee" value="${fee}">
        <input type="hidden" name="payment_method" value="${paymentMethod}">
      `;

      document.body.appendChild(form);
      form.submit();
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('paymentModal');
      if (event.target == modal) {
        modal.style.display = 'none';
      }

      const datePicker = document.getElementById('datePickerModal');
      if (event.target == datePicker) {
        datePicker.style.display = 'none';
      }
    }
  </script>
  <!-- Load flatpickr -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script>
    // Initialize flatpickr globally
    document.addEventListener('DOMContentLoaded', function() {
      try {
        flatpickr("#appointmentDate", {
          minDate: "today",
          dateFormat: "Y-m-d",
          disable: [
            function(date) {
              // Disable weekends
              return (date.getDay() === 0 || date.getDay() === 6);
            },
            // Disable past dates
            function(date) {
              return date.getTime() < Date.now();
            }
          ],
          onChange: function(selectedDates, dateStr) {
            selectedDate = dateStr;
          }
        });
      } catch (e) {
        console.error('Error initializing flatpickr:', e);
      }
    });
  </script>
</head>

<body>
  <!-- ... -->
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

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden">
  <div class="bg-white rounded-lg p-6 max-w-md w-full">
    <div class="modal-content"></div>
  </div>
</div>

<!-- ... -->
</body>
</html>
