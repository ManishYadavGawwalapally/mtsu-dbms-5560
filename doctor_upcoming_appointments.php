<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include('dbconnection.php');

if (!isset($_SESSION['loginid'])) {
    header("Location: index.php");
    exit();
}

$doctorid = $_SESSION['loginid'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentid = $_POST['appointmentid'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        $query = "UPDATE appointment SET status = 'approved' WHERE appointmentid = ?";
    } elseif ($action == 'delete') {
        $query = "UPDATE appointment SET status = 'rejected' WHERE appointmentid = ?";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $appointmentid);
    $stmt->execute();
}

$query = "
SELECT a.appointmentid, p.patientid, p.patientname, a.appointmentdate, a.appointmenttime, a.app_reason, a.status 
FROM appointment a
JOIN patient p ON a.patientid = p.patientid
WHERE a.doctorid = ? AND a.status = 'pending'
ORDER BY a.appointmentdate DESC, a.appointmenttime DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $doctorid);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Appointments</title>
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
            transition: all 0.3s;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 0;
            border-bottom: 1px solid var(--border-color);
        }

        .menu-item > a {
            display: block;
            padding: 15px 20px;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s;
        }

        .menu-item > a > span:first-child {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .menu-item > a:hover {
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

        table {
            background: rgba(0,0,0,0.5);
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid white;
        }

        button {
            padding: 6px 10px;
            cursor: pointer;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: var(--accent-color);
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

    </style>
</head>
<body>

    <header>
        <div class="logo">MediLog</div>
        <div class="user-info">
            <span>Welcome, Dr. <?php echo htmlspecialchars($doctorid); ?></span>
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
                    <span><i class="fas fa-notes-medical"></i> All Doctors</span>
                </a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <h1 class="dashboard-title">Upcoming Appointments</h1>
        
        <table>
            <tr>
                <th>Patient ID</th>
                <th>Patient Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['patientid'] ?></td>
                <td><?= $row['patientname'] ?></td>
                <td><?= $row['appointmentdate'] ?></td>
                <td><?= $row['appointmenttime'] ?></td>
                <td><?= $row['app_reason'] ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="appointmentid" value="<?= $row['appointmentid'] ?>">
                        <button name="action" value="approve">Approve</button>
                        <button name="action" value="delete">Reject</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

    </div>

</body>
</html>
