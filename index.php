<?php
session_start(); // main login page
?>

<?php include('login.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MediLog - Hospital DBMS</title>
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
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
        }

        /* Background Image Style */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('bg2.jpg') no-repeat center center;
            background-size: cover;
            z-index: -1;
        }

        /* Header/Nav Styles */
        nav {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        nav a {
            text-decoration: none;
            color: white;
            margin: 0 15px;
            transition: all 0.3s;
            font-size: 16px;
        }

        nav a:hover {
            color: rgba(255,255,255,0.8);
            transform: translateY(-2px);
        }

        /* Content Area */
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 65px; 
        }

        /* Footer Styles */
        footer {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
            text-align: center;
            padding: 15px 0;
            width: 100%;
            font-size: 14px;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
            position: relative;
            margin-top: auto; 
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            width: 50%;
            max-width: 600px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: var(--text-color);
            position: relative;
        }

        .modal-content h2 {
            margin-bottom: 20px;
            color: var(--primary-color);
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-content p {
            margin-bottom: 15px;
            line-height: 1.6;
            text-align: left;
        }

        .close-btn {
            margin-top: 20px;
            padding: 10px 25px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }

        .close-btn:hover {
            background-color: var(--secondary-color);
        }

        
        @media (max-width: 768px) {
            .modal-content {
                width: 90%;
            }

            nav {
                padding: 15px 20px;
                flex-direction: column;
                align-items: flex-start;
            }

            nav > div:last-child {
                margin-top: 15px;
                display: flex;
                flex-wrap: wrap;
            }

            nav a {
                margin: 5px 15px 5px 0;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo">MediLog</div>
        <div>
            <a href="#home"><i class="fas fa-home"></i> Home</a>
            <a href="javascript:void(0)" id="about-link"><i class="fas fa-info-circle"></i> About</a>
            <a href="javascript:void(0)" id="services-link"><i class="fas fa-stethoscope"></i> Services</a>
            <a href="javascript:void(0)" id="contact-link"><i class="fas fa-envelope"></i> Contact Us</a>
            <a href="javascript:void(0)" id="login-link"><i class="fas fa-sign-in-alt"></i> Login</a>
        </div>
    </nav>

    <div class="main-content">
        
    </div>

    <!-- About Modal -->
    <div id="about-modal" class="modal">
        <div class="modal-content">
            <h2>About MediLog</h2>
            <p>MediLog is a comprehensive web-based hospital management system designed to simplify and streamline healthcare management.</p>
            <p>It allows authorized users, such as doctors and patients, to manage appointments, access medical records, track treatments and diagnostics, and handle invoices.</p>
            <p>With features such as easy profile management, appointment scheduling, and secure access to patient information, MediLog aims to improve the efficiency of healthcare services, providing both doctors and patients with a seamless and secure experience.</p>
            <p>Whether it's managing patient data, viewing appointment schedules, or keeping track of medical history, MediLog ensures that healthcare professionals and patients have all the tools they need in one convenient platform.</p>
            <button class="close-btn" onclick="closeModal('about-modal')">Close</button>
        </div>
    </div>

    <!-- Services Modal -->
    <div id="services-modal" class="modal">
        <div class="modal-content">
            <h2>Our Services</h2>
            <p><strong>Appointment Management:</strong> Schedule, view, and manage doctor appointments for patients and healthcare professionals.</p>
            <p><strong>Patient Records Access:</strong> Secure access to comprehensive patient medical history, treatments, and diagnostic information.</p>
            <p><strong>Profile Management:</strong> Personalized profile management for both doctors and patients, with the ability to update and maintain personal details.</p>
            <p><strong>Treatment and Diagnostics Tracking:</strong> Track and update treatment progress and diagnostics for patients in real-time.</p>
            <button class="close-btn" onclick="closeModal('services-modal')">Close</button>
        </div>
    </div>

    <!-- Contact Modal -->
    <div id="contact-modal" class="modal">
        <div class="modal-content">
            <h2>Contact Us</h2>
            <p><i class="fas fa-envelope"></i> Email: medilog2025@mail.com</p>
            <p><i class="fas fa-phone"></i> Phone: (999)-(888)-(6789)</p>
            <p><i class="fas fa-map-marker-alt"></i> Address: 123 Healthcare Avenue, Medical District, MD 12345</p>
            <button class="close-btn" onclick="closeModal('contact-modal')">Close</button>
        </div>
    </div>

    <footer>
        &copy; 2025 MediLog. All Rights Reserved.
    </footer>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "flex";
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        document.getElementById('about-link').onclick = function() {
            openModal('about-modal');
        }

        document.getElementById('services-link').onclick = function() {
            openModal('services-modal');
        }

        document.getElementById('contact-link').onclick = function() {
            openModal('contact-modal');
        }

        document.getElementById('login-link').onclick = function() {
            openModal('login-modal');    
        }
        
        document.addEventListener("DOMContentLoaded", function () {
            var closeButton = document.querySelector("#login-modal .close-btn");
            if (closeButton) {
                closeButton.onclick = function () {
                    closeModal('login-modal');
                };
            }
        });
    </script>
</body>
</html>