<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('dbconnection.php');

// Check if the patient is logged in
if (!isset($_SESSION['loginid'])) {
    header("Location: index.php");
    exit();
}

$loginid = $_SESSION['loginid'];


$query = "SELECT patientname, bloodgroup, gender, dob, address, mobileno, city, pincode FROM patient WHERE loginid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $loginid);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();


$query = "SELECT a.appointmentid, a.appointmentdate, a.appointmenttime, d.doctorname, d.departmentid 
          FROM appointment a 
          JOIN doctor d ON a.doctorid = d.doctorid 
          WHERE a.patientid = ? 
          ORDER BY a.appointmentdate DESC, a.appointmenttime DESC 
          LIMIT 3";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $loginid);
$stmt->execute();
$appointments = $stmt->get_result();


$query = "SELECT p.prescriptionid, p.prescriptiondate, d.doctorname 
          FROM prescription p 
          JOIN doctor d ON p.doctorid = d.doctorid 
          WHERE p.patientid = ? 
          ORDER BY p.prescriptiondate DESC 
          LIMIT 3";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $loginid);
$stmt->execute();
$prescriptions = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
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
        
        .dashboard-title {
            font-size: 24px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            transition: all 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .card-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: bold;
        }
        
        .card-content {
            color: var(--light-text);
        }
        
        .card-list {
            list-style: none;
        }
        
        .card-list li {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .card-list li:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .profile-card {
            display: flex;
            gap: 20px;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 40px;
        }
        
        .profile-details {
            flex: 1;
        }
        
        .profile-details h3 {
            margin-bottom: 10px;
        }
        
        .profile-details p {
            margin-bottom: 5px;
            color: var(--light-text);
        }
        
        .view-all {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: bold;
        }
        
        .view-all:hover {
            text-decoration: underline;
        }
        
        /* Responsive Styles */
        @media (max-width: 991px) {
            .sidebar {
                width: 70px;
                z-index: 999;
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
                margin-top: 65px;
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
            
            .dashboard-cards {
                grid-template-columns: 1fr;
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
            <a href="profile.php">
                <span><i class="fas fa-user"></i> <span class="menu-text">My Profile</span></span>
            </a>
        </div>
        <div class="menu-item">
            <a href="javascript:void(0)" class="toggle-submenu">
                <span><i class="fas fa-calendar-check"></i> <span class="menu-text">Appointments</span></span>
                <i class="fas fa-chevron-down"></i>
            </a>
            <div class="submenu">
                <div class="submenu-item">
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
        <h1 class="dashboard-title">Patient Dashboard</h1>
        
        <div class="dashboard-cards">
            <div class="card profile-card">
                <div class="profile-avatar">
                    <?php echo substr(htmlspecialchars($patient['patientname']), 0, 1); ?>
                </div>
                <div class="profile-details">
                    <h3><?php echo htmlspecialchars($patient['patientname']); ?></h3>
                    <p><strong>Blood Group:</strong> <?php echo htmlspecialchars($patient['bloodgroup']); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($patient['gender']); ?></p>
                    <p><strong>Mobile:</strong> <?php echo htmlspecialchars($patient['mobileno']); ?></p>
                    <a href="profile.php" class="view-all">Update Profile</a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="card-title">Recent Appointments</div>
                </div>
                <div class="card-content">
                    <?php if ($appointments->num_rows > 0): ?>
                        <ul class="card-list">
                            <?php while ($row = $appointments->fetch_assoc()): ?>
                                <li>
                                    <div><strong><?php echo date('M d, Y', strtotime($row['appointmentdate'])); ?> at <?php echo date('h:i A', strtotime($row['appointmenttime'])); ?></strong></div>
                                    <div>Dr. <?php echo htmlspecialchars($row['doctorname']); ?> (<?php echo htmlspecialchars($row['departmentname']); ?>)</div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                        <a href="view_appointments.php" class="view-all">View All Appointments</a>
                    <?php else: ?>
                        <p>No appointments found. <a href="scheduleappointment.php">Schedule one now</a>.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-prescription"></i>
                    </div>
                    <div class="card-title">Recent Prescriptions</div>
                </div>
                <div class="card-content">
                    <?php if ($prescriptions->num_rows > 0): ?>
                        <ul class="card-list">
                            <?php while ($row = $prescriptions->fetch_assoc()): ?>
                                <li>
                                    <div><strong><?php echo date('M d, Y', strtotime($row['prescriptiondate'])); ?></strong></div>
                                    <div>Dr. <?php echo htmlspecialchars($row['doctorname']); ?></div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                        <a href="prescriptionrecords.php" class="view-all">View All Prescriptions</a>
                    <?php else: ?>
                        <p>No prescriptions found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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