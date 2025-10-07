<?php
require('db.php');
$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $voterid = $_POST["voterid"];
    $password = $_POST["password"];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $check_stmt = $conn->prepare("SELECT email FROM users WHERE email=? OR voter_id=?");
    $check_stmt->bind_param("ss", $email, $voterid);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $error = "Email or Voter ID already registered. Please use a different one.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users(username, email, voter_id, password) VALUES(?,?,?,?)");
        $stmt->bind_param("ssss", $name, $email, $voterid, $hashed_password);

        if ($stmt->execute()) {
            $message = "Registration successful! You can now <a href='login.php' class='alert-link'>log in</a>.";
        } else {
            $error = "Registration failed. Please try again.";
        }
        $stmt->close();
    }
    $check_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Voter Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-image: url('9270978_4116831.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Style for the solid white form container */
        .form-container {
            background-color: white;
            /* Changed to solid white */
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            color: #333;
            /* Changed text to a dark color for readability */
            position: relative;
        }

        /* No ::before pseudo-element needed */

        .form-label {
            color: #333;
            /* Changed text color */
        }

        .form-control {
            background-color: #f8f9fa;
            /* Light gray for input background */
            color: #333;
            /* Changed text color */
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
    </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="form-container p-5">
                    <h2 class="text-center mb-4 text-dark">Voter Registration Form</h2>
                    <?php
                    if ($message) {
                        echo '<div class="alert alert-success mt-3 text-center" id="message-alert">' . $message . '</div>';
                    } elseif ($error) {
                        echo '<div class="alert alert-danger mt-3 text-center" id="error-alert">' . $error . '</div>';
                    }
                    ?>
                    <form id="registrationForm" method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required />
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required />
                        </div>
                        <div class="mb-3">
                            <label for="voterId" class="form-label">Voter ID</label>
                            <input type="text" class="form-control" id="voterId" name="voterid" required />
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
                        <button type="submit" class="btn btn-primary w-100 mt-4">Register</button>
                    </form>
                    <p class="text-center mt-3 text-dark">Already have an account? <a href="login.php" class="text-info text-decoration-none">Login here</a></p>
                </div>
            </div>
        </div>
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