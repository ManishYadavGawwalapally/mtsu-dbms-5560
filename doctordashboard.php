<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('dbconnection.php');

if (!isset($_SESSION['loginid'])) {
    header("Location: index.php");
    exit();
}

$loginid = $_SESSION['loginid'];

// Fetch doctor details
$query = "SELECT doctorid, doctorname, departmentid, education, mobileno, status FROM doctor WHERE loginid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $loginid);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// Fetch department details
$query = "SELECT departmentname FROM department WHERE departmentid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor['departmentid']);
$stmt->execute();
$department_result = $stmt->get_result();
$department = $department_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: rgb(52, 152, 219);
            --secondary-color: rgb(41, 128, 185);
            --accent-color: rgb(231, 76, 60);
            --text-color: #333;
            --light-text: #777;
            --light-bg: rgb(245, 245, 245);
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
            <span>Welcome, Dr. <?php echo htmlspecialchars($doctor['doctorname']); ?></span>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </header>

    <div class="sidebar">
        <div class="sidebar-menu">
            <div class="menu-item">
                <a href="doctor_profile.php">
                    <span><i class="fas fa-user"></i> <span class="menu-text">Profile</span></span>
                </a>
            </div>
            <div class="menu-item">
                <a href="javascript:void(0)" class="toggle-submenu">
                    <span><i class="fas fa-calendar-check"></i> <span class="menu-text">Appointments</span></span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="submenu">
                    <div class="submenu-item">
                        <a href="doctor_upcoming_appointments.php">Upcoming Appointments</a>
                    </div>
                    <div class="submenu-item">
                        <a href="doctor_approved_appointments.php">Approved Appointment</a>
                    </div>
                </div>
            </div>
            <div class="menu-item">
                <a href="doctor_patient_details.php">
                    <span><i class="fas fa-notes-medical"></i> <span class="menu-text">All Patients</span></span>
                </a>
            </div>
            <div class="menu-item">
                <a href="doctor_details.php">
                    <span><i class="fas fa-prescription"></i> <span class="menu-text">All doctors</span></span>
                </a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <h1 class="dashboard-title">Doctor Dashboard</h1>

        <div class="dashboard-cards">
            <div class="card profile-card">
                <div class="profile-avatar">
                    <?php echo substr(htmlspecialchars($doctor['doctorname']), 0, 1); ?>
                </div>
                <div class="profile-details">
                    <h3><?php echo htmlspecialchars($doctor['doctorname']); ?></h3>
                    <p><strong>Department:</strong> <?php echo htmlspecialchars($department['departmentname']); ?></p>
                    <p><strong>Mobile:</strong> <?php echo htmlspecialchars($doctor['mobileno']); ?></p>
                    <p><strong>Education:</strong> <?php echo htmlspecialchars($doctor['education']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($doctor['status']); ?></p>
                    
                </div>
            </div>
        </div>
    </div>

    <script>
        
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
