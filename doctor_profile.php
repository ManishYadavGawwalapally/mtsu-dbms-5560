<?php
session_start();
include('dbconnection.php');

if (!isset($_SESSION['loginid'])) {
    header("Location: index.php");
    exit();
}

$loginid = $_SESSION['loginid'];


$query = "SELECT * FROM doctor WHERE loginid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $loginid);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// Fetch department name
$departmentName = '';
if (!empty($doctor['departmentid'])) {
    $deptQuery = "SELECT departmentname FROM department WHERE departmentid = ?";
    $deptStmt = $conn->prepare($deptQuery);
    $deptStmt->bind_param("i", $doctor['departmentid']);
    $deptStmt->execute();
    $deptResult = $deptStmt->get_result();
    if ($deptRow = $deptResult->fetch_assoc()) {
        $departmentName = $deptRow['departmentname'];
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $doctorname = $_POST['doctorname'];
    $education = $_POST['education'];
    $mobileno = $_POST['mobileno'];
    $departmentid = $_POST['departmentid'];
    $status = $_POST['status'];

    
    $updateQuery = "UPDATE doctor SET doctorname = ?, education = ?, mobileno = ?, departmentid = ?, status = ? WHERE loginid = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ssssss", $doctorname, $education, $mobileno, $departmentid, $status, $loginid);
    $updateStmt->execute();

    header("Location: doctordashboard.php?update=success");
    exit();
}
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
            --primary-color: rgb(52,152,219);
            --secondary-color: rgb(41,128,185);
            --accent-color: rgb(231,76,60);
            --text-color: #333;
            --light-text: #777;
            --light-bg: rgb(245,245,245);
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

        .menu-item, .submenu-item {
            border-bottom: 1px solid var(--border-color);
        }

        .menu-item a, .submenu-item a {
            display: block;
            padding: 15px 20px;
            color: var(--text-color);
            text-decoration: none;
        }

        .menu-item a:hover, .submenu-item a:hover {
            background: var(--light-bg);
            color: var(--primary-color);
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

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
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
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
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

        .card-content p {
            margin-bottom: 8px;
            color: var(--light-text);
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
            margin-bottom: 15px;
        }

        .edit-btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            text-align: center;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-btn:hover {
            background: var(--primary-color);
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

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 16px;
        }

        .form-submit-btn {
            width: 100%;
            padding: 10px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .form-submit-btn:hover {
            background: var(--accent-color);
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">MediLog</div>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($doctor['doctorname']); ?></span>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </header>

    <div class="sidebar">
        <div class="sidebar-menu">
            <div class="menu-item">
                <a href="doctordashboard.php">
                    <span><i class="fas fa-user-md"></i> <span class="menu-text">My Profile</span></span>
                </a>
            </div>
            <div class="menu-item">
                <a href="doctor_upcoming_appointments.php">
                    <span><i class="fas fa-calendar-check"></i> <span class="menu-text">Upcoming Appointments</span></span>
                </a>
            </div>
            <div class="menu-item">
                <a href="doctor_approved_appointments.php">
                    <span><i class="fas fa-calendar-check"></i> <span class="menu-text">Approved Appointments</span></span>
                </a>
            </div>
            <div class="menu-item">
                <a href="doctor_patient_details.php">
                    <span><i class="fas fa-prescription-bottle-alt"></i> <span class="menu-text">All Patients</span></span>
                </a>
            </div>
            <div class="menu-item">
                <a href="doctor_details.php">
                    <span><i class="fas fa-prescription-bottle-alt"></i> <span class="menu-text">All Doctors</span></span>
                </a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <h1 class="dashboard-title">Doctor Dashboard</h1>

        <?php if (isset($_GET['update']) && $_GET['update'] == 'success'): ?>
            <div style="background: #28a745; color: white; padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center;">
                Profile updated successfully!
            </div>
        <?php endif; ?>

        <div class="dashboard-cards">
            <div class="card">
                <div class="profile-avatar">
                    <?php echo substr(htmlspecialchars($doctor['doctorname']), 0, 1); ?>
                </div>
                <div class="card-header">
                    <div class="card-icon"><i class="fas fa-user-md"></i></div>
                    <div class="card-title">Profile Overview</div>
                </div>
                <div class="card-content">
                    <form method="POST" action="doctordashboard.php">
                        <div class="form-group">
                            <label for="doctorname">Name</label>
                            <input type="text" name="doctorname" id="doctorname" value="<?php echo htmlspecialchars($doctor['doctorname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="education">Education Degree</label>
                            <input type="text" name="education" id="education" value="<?php echo htmlspecialchars($doctor['education']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="mobileno">Mobile Number</label>
                            <input type="text" name="mobileno" id="mobileno" value="<?php echo htmlspecialchars($doctor['mobileno']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="departmentid">Department</label>
                            <select name="departmentid" id="departmentid" required>
                                <option value="<?php echo htmlspecialchars($doctor['departmentid']); ?>"><?php echo htmlspecialchars($departmentName); ?></option>
                                
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <input type="text" name="status" id="status" value="<?php echo htmlspecialchars($doctor['status']); ?>" required>
                        </div>
                        <button type="submit" class="form-submit-btn">Update Profile</button>
                    </form>
                </div>
                <button type="button" class="edit-btn">Save Changes</button>
            </div>
        </div>
    </div>
</body>
</html>
