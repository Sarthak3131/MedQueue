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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors - MedQueue</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">MedQueue</h1>
            <div class="flex space-x-4">
                <a href="index.php" class="hover:text-blue-200">Home</a>
                <a href="doctor.php" class="hover:text-blue-200">Doctors</a>
                <a href="appointment.php" class="hover:text-blue-200">Appointments</a>
                <a href="feedback.php" class="hover:text-blue-200">Feedback</a>
                <a href="Backend/logout.php" class="hover:text-blue-200">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8 text-blue-600">Available Doctors</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($doctors as $doctor): ?>
                <div class="bg-white rounded-lg shadow-md p-6 text-center cursor-pointer hover:shadow-lg transition-shadow">
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
    </div>

    <script>
        function bookAppointment(doctorId, doctorName, department) {
            // Create a form to submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'Backend/book.php';
            form.style.display = 'none';

            // Add form fields
            const fields = [
                { name: 'doctor_name', value: doctorName },
                { name: 'department', value: department }
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
    </script>
</body>
</html>
