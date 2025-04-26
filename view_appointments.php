<?php
session_start();
include('dbconnection.php');

// Check if patient is logged in
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

// Fetch appointments joined with doctor information
$query = "SELECT a.*, d.doctorname, d.departmentid 
          FROM appointment a 
          LEFT JOIN doctor d ON a.doctorid = d.doctorid 
          WHERE a.patientid = ? 
          ORDER BY a.appointmentdate DESC, a.appointmenttime DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patientid);
$stmt->execute();
$appointments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
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

        /* Header Styles */
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

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: white;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            position: fixed;
            top: 65px;
            bottom: 0;
            left: 0;
            overflow-y: auto;
            transition: all 0.3s;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-item {
            padding: 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .menu-item > a, .submenu-item > a {
            display: block;
            padding: 15px 20px;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .menu-item > a {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .menu-item > a > span:first-child {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .menu-item > a:hover, .submenu-item > a:hover {
            background: var(--light-bg);
            color: var(--primary-color);
        }
        
        .submenu {
            display: none;
            background: #f9f9f9;
        }
        
        .submenu-item > a {
            padding-left: 50px;
            font-size: 14px;
        }
        
        .active-menu > a {
            color: var(--primary-color);
            font-weight: bold;
        }

        /* Main Content Styles */
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
        
        /* Appointment Table Styles */
        .appointments-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .appointments-table th, .appointments-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .appointments-table th {
            background: var(--primary-color);
            color: white;
            font-weight: bold;
        }
        
        .appointments-table tr:last-child td {
            border-bottom: none;
        }
        
        .appointments-table tr:hover {
            background: var(--light-bg);
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-active {
            background: rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }
        
        .status-cancelled {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }
        
        .status-completed {
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }
        
        .status-pending {
            background: rgba(241, 196, 15, 0.2);
            color: #f39c12;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .empty-state i {
            font-size: 48px;
            color: var(--light-text);
            margin-bottom: 15px;
        }
        
        .empty-state p {
            color: var(--light-text);
            margin-bottom: 15px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: var(--primary-color);
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background: var(--secondary-color);
        }
        
        /* Responsive Styles */
        @media (max-width: 991px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar-menu span.menu-text, 
            .sidebar-menu .submenu,
            .sidebar-menu .fa-chevron-down {
                display: none;
            }
            
            .sidebar-menu .menu-item > a {
                justify-content: center;
                padding: 15px;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .sidebar:hover {
                width: 250px;
            }
            
            .sidebar:hover .menu-text,
            .sidebar:hover .fa-chevron-down {
                display: inline-block;
            }
            
            .sidebar:hover .menu-item > a {
                justify-content: space-between;
                padding: 15px 20px;
            }
        }
        
        @media (max-width: 767px) {
            .main-content {
                margin-left: 0;
            }
            
            .sidebar {
                left: -250px;
                width: 250px;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .appointments-table {
                display: block;
                overflow-x: auto;
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
                <a href="patientdashboard.php">
                    <span><i class="fas fa-home"></i> <span class="menu-text">Dashboard</span></span>
                </a>
            </div>
            <div class="menu-item">
                <a href="profile.php">
                    <span><i class="fas fa-user"></i> <span class="menu-text">My Profile</span></span>
                </a>
            </div>
            <div class="menu-item active-menu">
                <a href="javascript:void(0)" class="toggle-submenu">
                    <span><i class="fas fa-calendar-check"></i> <span class="menu-text">Appointments</span></span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="submenu">
                    <div class="submenu-item active-menu">
                        <a href="view_appointments.php">View Appointments</a>
                    </div>
                    <div class="submenu-item">
                        <a href="scheduleappointment.php">Schedule Appointment</a>
                    </div>
                </div>
            </div>
            <div class="menu-item">
                <a href="treatmentrecords.php">
                    <span><i class="fas fa-notes-medical"></i> <span class="menu-text">Treatment Records</span></span>
                </a>
            </div>
            <div class="menu-item">
                <a href="prescriptionrecords.php">
                    <span><i class="fas fa-prescription"></i> <span class="menu-text">Prescription Records</span></span>
                </a>
            </div>
            <div class="menu-item">
                <a href="billingrecords.php">
                    <span><i class="fas fa-file-invoice-dollar"></i> <span class="menu-text">Billing Records</span></span>
                </a>
            </div>
        </div>
    </div>
    
    <div class="main-content">
        <h1 class="page-title">My Appointments</h1>
        
        <?php if ($appointments->num_rows > 0): ?>
            <table class="appointments-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Doctor</th>
                        <th>Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $appointments->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($row['appointmentdate'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($row['appointmenttime'])); ?></td>
                            <td>Dr. <?php echo htmlspecialchars($row['doctorname']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointmenttype']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h2>No Appointments Found</h2>
                <p>You don't have any appointments scheduled yet.</p>
                <a href="scheduleappointment.php" class="btn">Schedule An Appointment</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Toggle submenu visibility
        document.querySelectorAll('.toggle-submenu').forEach(function(element) {
            element.addEventListener('click', function() {
                const submenu = this.nextElementSibling;
                if (submenu.style.display === 'block') {
                    submenu.style.display = 'none';
                    this.querySelector('.fa-chevron-down').classList.remove('fa-chevron-up');
                    this.querySelector('.fa-chevron-down').classList.add('fa-chevron-down');
                } else {
                    submenu.style.display = 'block';
                    this.querySelector('.fa-chevron-down').classList.remove('fa-chevron-down');
                    this.querySelector('.fa-chevron-down').classList.add('fa-chevron-up');
                }
            });
        });

        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.createElement('div');
            menuToggle.className = 'menu-toggle';
            menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            menuToggle.style.display = 'none';
            menuToggle.style.cursor = 'pointer';
            
            document.querySelector('.logo').before(menuToggle);
            
            menuToggle.addEventListener('click', function() {
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