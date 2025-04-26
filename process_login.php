
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('dbconnection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

   
    $query = "SELECT * FROM patient WHERE loginid = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['loginid'] = $row['loginid'];
        $_SESSION['patientname'] = $row['patientname'];
        header("Location: patientdashboard.php");
        exit();
    }

    $query = "SELECT * FROM doctor WHERE loginid = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['loginid'] = $row['loginid'];
        $_SESSION['doctorname'] = $row['doctorname'];
        header("Location: doctordashboard.php");
        exit();
    } else {
        echo "Invalid login credentials";
    }
}
?>
