<?php
require "db.php";
session_start();
$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $candidateId = $_POST["candidateId"];
    $orgpassword = $_POST["password"];

    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $candidateId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $hashed_password_from_db = $user['password'];

        if (password_verify($orgpassword, $hashed_password_from_db)) {
            $_SESSION["candidateId"] = $candidateId;
            header("Location: votingpage.php");
            exit();
        } else {
            $error = "Invalid Candidate ID or Password";
        }
    } else {
        $error = "Invalid Candidate ID or Password";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Candidate Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-image: url('9270978_4116831.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .form-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            color: #333;
            position: relative;
        }
        .form-label {
            color: #333;
        }
        .form-control {
            background-color: #f8f9fa;
            color: #333;
            border: 1px solid #ccc;
        }
        .form-control:focus {
            background-color: #e9ecef;
            color: #333;
            box-shadow: 0 0 0 0.25rem rgba(0, 128, 255, 0.25);
            border-color: #0080ff;
        }
        .btn-primary {
            background-color: #0080ff;
            border-color: #0080ff;
        }
        .btn-primary:hover {
            background-color: #0066cc;
            border-color: #0066cc;
        }
        .alert {
            margin-top: 1rem;
        }
        /* New styling for the icon inside the input group */
        .input-group-text {
            background-color: #f8f9fa;
            border-left: none;
            cursor: pointer;
        }
        .input-group-text:hover {
            color: #0066cc;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card form-container p-4 shadow-sm" style="max-width: 400px; width: 100%;">
        <h2 class="card-title text-center text-dark mb-4">Candidate Login</h2>
        
        <form id="loginForm" action="login.php" method="POST">
            <div class="mb-3">
                <label for="candidateId" class="form-label">Candidate ID</label>
                <input type="text" class="form-control" id="candidateId" name="candidateId" required />
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required />
                    <button class="btn-outline-dark input-group-text" type="button" id="togglePassword">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        
        <p class="text-center mt-3">
            Don't Have an Account? <br />
            <a href="registration.php" class="text-info text-decoration-none">Register Here</a>
        </p>
        
        <?php
        if (isset($message) && $message != '') {
            echo '<div class="alert alert-success mt-3 text-center" id="message-alert">' . $message . '</div>';
        } elseif (isset($error) && $error != '') {
            echo '<div class="alert alert-danger mt-3 text-center" id="error-alert">' . $error . '</div>';
        }
        ?>    
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(() => {
            const messageAlert = document.getElementById('message-alert');
            const errorAlert = document.getElementById('error-alert');
            if (messageAlert) {
                messageAlert.remove();
            }
            if (errorAlert) {
                errorAlert.remove();
            }
        }, 3000);

        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            // Toggle the type attribute
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle the eye icon
            this.querySelector('i').classList.toggle('bi-eye-fill');
            this.querySelector('i').classList.toggle('bi-eye-slash-fill');
        });
    </script>
</body>
</html>