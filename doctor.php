<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors - MedQueue</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="images/d1.png">
    <style>
        .doctor-card {
            transition: transform 0.3s ease;
        }
        .doctor-card:hover {
            transform: translateY(-5px);
        }
        .department-tag {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }
        .general { background-color: #4CAF50; color: white; }
        .cardiology { background-color: #2196F3; color: white; }
        .diagnostics { background-color: #9C27B0; color: white; }
    </style>
</head>
<body class="bg-gradient-to-b from-blue-50 via-white to-blue-100 min-h-screen text-gray-800 font-sans">
    <!-- Navbar -->
    <header class="bg-blue-600 text-white p-4 shadow-md">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-4">
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
    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-wrap justify-center gap-2 mb-6">
            <button onclick="filterDoctors('all')" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold transition duration-200">All</button>
            <button onclick="filterDoctors('General')" class="px-4 py-2 rounded-lg bg-green-100 hover:bg-green-200 text-green-800 font-semibold transition duration-200">General</button>
            <button onclick="filterDoctors('Cardiology')" class="px-4 py-2 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-800 font-semibold transition duration-200">Cardiology</button>
            <button onclick="filterDoctors('Diagnostics')" class="px-4 py-2 rounded-lg bg-purple-100 hover:bg-purple-200 text-purple-800 font-semibold transition duration-200">Diagnostics</button>
        </div>
    </div>

    <!-- Doctor Cards -->
    <main class="container mx-auto px-4">
        <div id="doctor-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- JS Injected -->
        </div>
    </main>

    <!-- Booking Modal -->
    <div id="booking-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <form method="POST" action="Backend/book.php" class="bg-white p-6 rounded-xl shadow-xl space-y-4 w-full max-w-md animate-fadeIn">
            <h2 class="text-xl font-bold text-center text-blue-800 mb-6">Book Appointment</h2>
            <input type="hidden" name="doctor_name" id="doctor-name-input">
            <input type="hidden" name="department" id="department-input">

            <div class="space-y-4">
                <label class="block">
                    <span class="text-gray-700 font-semibold">Choose Date</span>
                    <input type="date" name="appointment_date" required class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 transition duration-200">
                </label>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition duration-200">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">Confirm</button>
            </div>
        </form>
    </div>

    <!-- Script -->
    <script>
        // Doctor data
        const doctors = [
            { name: "Dr. Anjali Verma", dept: "General", image: "images/anjali.jpg", id: 1 },
            { name: "Dr. Rajeev Menon", dept: "Cardiology", image: "images/rajeev.jpg", id: 2 },
            { name: "Dr. Pooja Singh", dept: "Diagnostics", image: "images/pooja.avif", id: 3 },
            { name: "Dr. Ravi Kumar", dept: "General", image: "images/ravi.jpeg", id: 4 }
        ];

        // Render doctors
        function renderDoctors(filter = "all") {
            const container = document.getElementById("doctor-container");
            container.innerHTML = "";
            const filtered = filter === "all" ? doctors : doctors.filter(doc => doc.dept === filter);
            
            filtered.forEach(doc => {
                const card = document.createElement("div");
                card.className = "doctor-card bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border border-blue-100";
                card.innerHTML = `
                    <div class="doctor-image w-full h-48 object-cover rounded-lg mb-4 shadow-sm overflow-hidden">
                        <img src="${doc.image}" alt="${doc.name}" class="w-full h-full object-cover transition-transform duration-300">
                    </div>
                    <h3 class="text-xl font-bold text-blue-800 mb-2">${doc.name}</h3>
                    <span class="department-tag ${doc.dept.toLowerCase()}">${doc.dept}</span>
                    <button onclick="openModal(${doc.id}, '${doc.name}', '${doc.dept}')" class="w-full mt-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-500 text-white px-4 py-2 rounded-lg shadow-sm transition duration-200">
                        Book Appointment
                    </button>
                `;
                container.appendChild(card);
            });
        }

        // Modal functions
        function openModal(id, name, dept) {
            document.getElementById("doctor-name-input").value = name;
            document.getElementById("department-input").value = dept;
            document.getElementById("booking-modal").classList.remove("hidden");
        }

        function closeModal() {
            document.getElementById("booking-modal").classList.add("hidden");
        }

        // Filter function
        function filterDoctors(category) {
            renderDoctors(category);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            renderDoctors("all");
            
            // Close modal when clicking outside
            window.onclick = function(event) {
                const modal = document.getElementById('booking-modal');
                if (event.target == modal) {
                    modal.classList.add('hidden');
                }
            }
        });
    </script>
</body>
</html>
