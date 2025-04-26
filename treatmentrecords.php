<?php
session_start();
include('dbconnection.php');

// patient treatment records
if (!isset($_SESSION['loginid'])) {
    header("Location: index.php");
    exit();
}

$loginid = $_SESSION['loginid'];

// Fetch patient details
$query = "SELECT patientid, patientname FROM patient WHERE loginid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $loginid);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$patientid = $patient['patientid'];

// Fetch treatment records
$query = "SELECT * FROM treatment_records WHERE patientid = ? ORDER BY treatment_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patientid);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treatment Records</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --accent-color: #e74c3c;
            --text-color: #333;
            --light-text: #777;
            --light-bg: #f5f5f5;
            --border-color: #ddd;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--light-bg);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            z-index: 1000;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info a {
            color: white;
            text-decoration: none;
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            transition: all 0.3s;
        }

        .user-info a:hover {
            background: rgba(255,255,255,0.3);
        }

        .sidebar {
            width: 250px;
            background: white;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            position: fixed;
            top: 65px;
            bottom: 0;
            left: 0;
            overflow-y: auto;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            border-bottom: 1px solid var(--border-color);
        }

        .menu-item > a {
            display: block;
            padding: 15px 20px;
            color: var(--text-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .menu-item > a:hover {
            background: var(--light-bg);
            color: var(--primary-color);
        }

        .active-menu > a {
            color: var(--primary-color);
            font-weight: bold;
        }

        .main-content {
            margin-left: 250px;
            margin-top: 65px;
            padding: 20px;
            flex: 1;
        }

        .page-title {
            font-size: 24px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .records-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .records-table th, .records-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .records-table th {
            background-color: var(--primary-color);
            color: white;
        }

        .records-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        @media (max-width: 767px) {
            .sidebar {
                left: -250px;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
                position: absolute;
                left: 20px;
                cursor: pointer;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">MediLog</div>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($patient['patientname']); ?></span>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </header>

    <div class="sidebar">
        <div class="sidebar-menu">
            <div class="menu-item">
                <a href="patientdashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            </div>
            <div class="menu-item">
                <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
            </div>
            <div class="menu-item">
                <a href="scheduleappointment.php"><i class="fas fa-calendar-plus"></i> Schedule Appointment</a>
            </div>
            <div class="menu-item">
                <a href="view_appointments.php"><i class="fas fa-calendar-check"></i> View Appointments</a>
            </div>
            <div class="menu-item active-menu">
                <a href="treatmentrecords.php"><i class="fas fa-notes-medical"></i> Treatment Records</a>
            </div>
            <div class="menu-item">
                <a href="prescriptionrecords.php"><i class="fas fa-prescription"></i> Prescription Records</a>
            </div>
            <div class="menu-item">
                <a href="billingrecords.php"><i class="fas fa-file-invoice-dollar"></i> Billing Records</a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <h1 class="page-title">Your Treatment Records</h1>
        <table class="records-table">
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Description</th>
                <th>Doctor ID</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['treatment_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['treatment_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['treatment_description']); ?></td>
                    <td><?php echo htmlspecialchars($row['doctorid']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script>
        // Mobile menu toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.createElement('div');
            menuToggle.className = 'menu-toggle';
            menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            document.querySelector('.logo').before(menuToggle);

            menuToggle.addEventListener('click', function () {
                document.querySelector('.sidebar').classList.toggle('active');
            });

            function checkScreenSize() {
                if (window.innerWidth <= 767) {
                    menuToggle.style.display = 'block';
                } else {
                    menuToggle.style.display = 'none';
                    document.querySelector('.sidebar').classList.remove('active');
                }
            }

            window.addEventListener('resize', checkScreenSize);
            checkScreenSize();
        });
    </script>
</body>
</html>
