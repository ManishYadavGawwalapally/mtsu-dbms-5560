<?php
session_start(); // main page login form pop up
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - MediLog</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: monospace;
            color: white;
            background: url('bg1.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 101;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .modal-content input {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            text-align: center;
        }

        .modal-content button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #ff6347;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
        }

        .modal-content button:hover {
            background-color: #e04b38;
        }
    </style>
</head>
<body>

    <div id="login-modal" class="modal">
        <div class="modal-content">
            <h2>Login</h2>
            <form action="process_login.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            
            <button class="close-btn" onclick="closeModal('login-modal')">Close</button>

        </div>
    </div>

    <script>
        function openModal() {
        document.getElementById('login-modal').style.display = "flex";
    }

    function closeModal() {
        document.getElementById('login-modal').style.display = "none";
    }

    document.getElementById('login-link').onclick = function() {
                openModal('login-modal');    
        }
    </script>
</body>
</html>
