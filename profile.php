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


$query = "SELECT patientid, patientname, address, mobileno, city, loginid, bloodgroup, gender, dob, status, pincode FROM patient WHERE loginid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $loginid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $patient = $result->fetch_assoc();
} else {
    echo "Error fetching patient details.";
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $patientname = $_POST['patientname'];
    $address = $_POST['address'];
    $mobileno = $_POST['mobileno'];
    $city = $_POST['city'];
    $bloodgroup = $_POST['bloodgroup'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $pincode = $_POST['pincode'];
    
    
    $update_query = "UPDATE patient SET patientname = ?, address = ?, mobileno = ?, city = ?, bloodgroup = ?, gender = ?, dob = ?, pincode = ? WHERE loginid = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sssssssss", $patientname, $address, $mobileno, $city, $bloodgroup, $gender, $dob, $pincode, $loginid);
    
    if ($update_stmt->execute()) {
        
        $stmt->execute();
        $result = $stmt->get_result();
        $patient = $result->fetch_assoc();
        $update_message = "Profile updated successfully!";
    } else {
        $update_error = "Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
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
            --success-color: #2ecc71;
            --error-color: #e74c3c;
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
        
        .profile-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
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
        
        .profile-info h2 {
            margin-bottom: 5px;
        }
        
        .profile-info p {
            color: var(--light-text);
        }
        
        .profile-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .detail-item {
            flex: 1 1 calc(50% - 20px);
            margin-bottom: 20px;
        }
        
        .detail-item label {
            display: block;
            font-weight: bold;
            color: var(--light-text);
            margin-bottom: 5px;
        }
        
        .detail-item p {
            font-size: 16px;
        }
        
        .profile-form .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .form-group {
            flex: 1 1 calc(50% - 20px);
        }
        
        .form-group label {
            display: block;
            font-weight: bold;
            color: var(--light-text);
            margin-bottom: 5px;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 16px;
        }
        
        .form-action {
            margin-top: 20px;
            text-align: right;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--secondary-color);
        }
        
        .tabs {
            display: flex;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 10px 20px;
            border: 1px solid var(--border-color);
            background: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .tab:first-child {
            border-radius: 4px 0 0 4px;
        }
        
        .tab:last-child {
            border-radius: 0 4px 4px 0;
        }
        
        .tab.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: rgba(46, 204, 113, 0.1);
            border: 1px solid var(--success-color);
            color: var(--success-color);
        }
        
        .alert-error {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid var(--error-color);
            color: var(--error-color);
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
            
            .detail-item, .form-group {
                flex: 1 1 100%;
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
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-avatar {
                margin: 0 auto;
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
            <div class="menu-item active-menu">
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
        <h1 class="page-title">My Profile</h1>
        
        <?php if (isset($update_message)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $update_message; ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($update_error)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo $update_error; ?>
        </div>
        <?php endif; ?>
        
        <div class="tabs">
            <div class="tab active" data-tab="view">View Profile</div>
            <div class="tab" data-tab="edit">Edit Profile</div>
        </div>
        
        <div class="tab-content active" id="view-tab">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <?php echo substr(htmlspecialchars($patient['patientname']), 0, 1); ?>
                    </div>
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($patient['patientname']); ?></h2>
                        <p>Patient ID: <?php echo htmlspecialchars($patient['patientid']); ?></p>
                    </div>
                </div>
                
                <div class="profile-details">
                    <div class="detail-item">
                        <label>Login ID</label>
                        <p><?php echo htmlspecialchars($patient['loginid']); ?></p>
                    </div>
                    <div class="detail-item">
                        <label>Mobile Number</label>
                        <p><?php echo htmlspecialchars($patient['mobileno']); ?></p>
                    </div>
                    <div class="detail-item">
                        <label>Gender</label>
                        <p><?php echo htmlspecialchars($patient['gender']); ?></p>
                    </div>
                    <div class="detail-item">
                        <label>Date of Birth</label>
                        <p><?php echo htmlspecialchars($patient['dob']); ?></p>
                    </div>
                    <div class="detail-item">
                        <label>Blood Group</label>
                        <p><?php echo htmlspecialchars($patient['bloodgroup']); ?></p>
                    </div>
                    <div class="detail-item">
                        <label>Address</label>
                        <p><?php echo htmlspecialchars($patient['address']); ?></p>
                    </div>
                    <div class="detail-item">
                        <label>City</label>
                        <p><?php echo htmlspecialchars($patient['city']); ?></p>
                    </div>
                    <div class="detail-item">
                        <label>Pincode</label>
                        <p><?php echo htmlspecialchars($patient['pincode']); ?></p>
                    </div>
                    <div class="detail-item">
                        <label>Status</label>
                        <p><?php echo htmlspecialchars($patient['status']); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="tab-content" id="edit-tab">
            <div class="profile-card">
                <form class="profile-form" method="post" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="patientname">Full Name</label>
                            <input type="text" id="patientname" name="patientname" value="<?php echo htmlspecialchars($patient['patientname']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="mobileno">Mobile Number</label>
                            <input type="tel" id="mobileno" name="mobileno" value="<?php echo htmlspecialchars($patient['mobileno']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" required>
                                <option value="Male" <?php if($patient['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                <option value="Female" <?php if($patient['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                <option value="Other" <?php if($patient['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($patient['dob']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bloodgroup">Blood Group</label>
                            <select id="bloodgroup" name="bloodgroup" required>
                                <option value="A+" <?php if($patient['bloodgroup'] == 'A+') echo 'selected'; ?>>A+</option>
                                <option value="A-" <?php if($patient['bloodgroup'] == 'A-') echo 'selected'; ?>>A-</option>
                                <option value="B+" <?php if($patient['bloodgroup'] == 'B+') echo 'selected'; ?>>B+</option>
                                <option value="B-" <?php if($patient['bloodgroup'] == 'B-') echo 'selected'; ?>>B-</option>
                                <option value="AB+" <?php if($patient['bloodgroup'] == 'AB+') echo 'selected'; ?>>AB+</option>
                                <option value="AB-" <?php if($patient['bloodgroup'] == 'AB-') echo 'selected'; ?>>AB-</option>
                                <option value="O+" <?php if($patient['bloodgroup'] == 'O+') echo 'selected'; ?>>O+</option>
                                <option value="O-" <?php if($patient['bloodgroup'] == 'O-') echo 'selected'; ?>>O-</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($patient['address']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($patient['city']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="pincode">Pincode</label>
                            <input type="text" id="pincode" name="pincode" value="<?php echo htmlspecialchars($patient['pincode']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-action">
                        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Tab Functionality
        document.querySelectorAll('.tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.tab').forEach(function(t) {
                    t.classList.remove('active');
                });
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(function(content) {
                    content.classList.remove('active');
                });
                
                // Show the selected tab content
                document.getElementById(this.getAttribute('data-tab') + '-tab').classList.add('active');
            });
        });

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