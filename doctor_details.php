<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include('dbconnection.php');


if (!isset($_SESSION['loginid'])) {
    header("Location: index.php");
    exit();
}


$query = "
SELECT d.doctorid, d.doctorname, d.education, d.status, 
       t.start_time, t.end_time, 
       dep.departmentname
FROM doctor d
LEFT JOIN doctor_timings t ON d.doctorid = t.doctorid
LEFT JOIN department dep ON d.departmentid = dep.departmentid
ORDER BY d.doctorid ASC";

$result = $conn->query($query);


$loginid = $_SESSION['loginid'];
$query = "SELECT doctorid, doctorname, departmentid FROM doctor WHERE loginid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $loginid);
$stmt->execute();
$doctor_result = $stmt->get_result();
$doctor = $doctor_result->fetch_assoc();


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
    <title>Doctor Details</title>
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

        /* Table Styles */
        table {
            width: 100%;
            background: rgba(0, 0, 0, 0.6);
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid var(--border-color);
            color: white;
        }

        th {
            background-color: var(--primary-color);
        }

        tr:nth-child(even) {
            background-color: rgba(0, 0, 0, 0.4);
        }

        /* Responsive Styles */
        @media (max-width: 991px) {
            .sidebar {
                width: 70px;
            }

            .main-content {
                margin-left: 70px;
            }

            .sidebar:hover {
                width: 250px;
            }

            .sidebar:hover .menu-text {
                display: inline-block;
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
                    <span><i class="fas fa-user"></i> Profile</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="doctor_upcoming_appointments.php">
                    <span><i class="fas fa-calendar-check"></i> Upcoming Appointments</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="doctor_approved_appointments.php">
                    <span><i class="fas fa-calendar-check"></i> Approved Appointments</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="doctor_patient_details.php">
                    <span><i class="fas fa-notes-medical"></i> All Patients</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="doctor_details.php">
                    <span><i class="fas fa-users"></i> All Doctors</span>
                </a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <h2 class="dashboard-title">Doctor Details</h2>
        <table>
            <tr>
                <th>Doctor ID</th>
                <th>Doctor Name</th>
                <th>Department</th>
                <th>Education</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['doctorid']) ?></td>
                <td><?= htmlspecialchars($row['doctorname']) ?></td>
                <td><?= htmlspecialchars($row['departmentname']) ?></td>
                <td><?= htmlspecialchars($row['education']) ?></td>
                <td><?= htmlspecialchars($row['start_time']) ?></td>
                <td><?= htmlspecialchars($row['end_time']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
