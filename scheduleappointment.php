<?php
session_start();
include('dbconnection.php');

// patient appointment schedule page
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

// Fetch doctors for dropdown
$doctorQuery = "SELECT doctorid, doctorname FROM doctor WHERE status='Active'";
$doctorResult = $conn->query($doctorQuery);

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointmenttype = $_POST['appointmenttype'];
    $appointmentdate = $_POST['appointmentdate'];
    $appointmenttime = $_POST['appointmenttime'];
    $doctorid = $_POST['doctorid'];
    $status = 'Pending';
    
    $query = "INSERT INTO appointment (appointmenttype, patientid, appointmentdate, appointmenttime, doctorid, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sissss", $appointmenttype, $patientid, $appointmentdate, $appointmenttime, $doctorid, $status);
    
    if ($stmt->execute()) {
        $message = "<div class='success-message'>Appointment scheduled successfully!</div>";
    } else {
        $message = "<div class='error-message'>Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Appointment</title>
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
        
        .menu-item > a {
            display: block;
            padding: 15px 20px;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s;
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
        
        /* Form Styles */
        .appointment-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 16px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background: var(--secondary-color);
        }
        
        .success-message {
            background: rgba(46, 204, 113, 0.2);
            color: #27ae60;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .error-message {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        /* Responsive Styles */
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
                <a href="patientdashboard.php">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </div>
            <div class="menu-item">
                <a href="profile.php">
                    <i class="fas fa-user"></i> My Profile
                </a>
            </div>
            <div class="menu-item active-menu">
                <a href="scheduleappointment.php">
                    <i class="fas fa-calendar-plus"></i> Schedule Appointment
                </a>
            </div>
            <div class="menu-item">
                <a href="view_appointments.php">
                    <i class="fas fa-calendar-check"></i> View Appointments
                </a>
            </div>
            <div class="menu-item">
                <a href="treatmentrecords.php">
                    <i class="fas fa-notes-medical"></i> Treatment Records
                </a>
            </div>
            <div class="menu-item">
                <a href="prescriptionrecords.php">
                    <i class="fas fa-prescription"></i> Prescription Records
                </a>
            </div>
            <div class="menu-item">
                <a href="billingrecords.php">
                    <i class="fas fa-file-invoice-dollar"></i> Billing Records
                </a>
            </div>
        </div>
    </div>
    
    <div class="main-content">
        <h1 class="page-title">Schedule an Appointment</h1>
        
        <?php echo $message; ?>
        
        <div class="appointment-form">
            <form method="POST">
                <div class="form-group">
                    <label for="appointmenttype">Appointment Type:</label>
                    <select id="appointmenttype" name="appointmenttype" class="form-control" required>
                        <option value="">-- Select Type --</option>
                        <option value="New Visit">New Visit</option>
                        <option value="Follow-up">Follow-up</option>
                        <option value="Consultation">Consultation</option>
                        <option value="Emergency">Emergency</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="appointmentdate">Appointment Date:</label>
                    <input type="date" id="appointmentdate" name="appointmentdate" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="appointmenttime">Appointment Time:</label>
                    <input type="time" id="appointmenttime" name="appointmenttime" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="doctorid">Select Doctor:</label>
                    <select id="doctorid" name="doctorid" class="form-control" required>
                        <option value="">-- Select Doctor --</option>
                        <?php while ($doctor = $doctorResult->fetch_assoc()): ?>
                            <option value="<?php echo $doctor['doctorid']; ?>">
                                Dr. <?php echo htmlspecialchars($doctor['doctorname']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Schedule Appointment</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mobile menu toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.createElement('div');
            menuToggle.className = 'menu-toggle';
            menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            
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