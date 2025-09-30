<?php
require "db.php";
session_start();

// Check if the user is logged in. If not, redirect to the login page.
if (!isset($_SESSION['candidateId'])) {
    header("Location: login.php");
    exit();
}

// Check for a POST request to process a vote
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $candidate_name = $_POST["candidate_name"];
    $logged_in_user_id = $_SESSION['candidateId'];

    // 1. Check if the user has already voted
    $check_vote_stmt = $conn->prepare("SELECT has_voted FROM users WHERE email = ?");
    $check_vote_stmt->bind_param("s", $logged_in_user_id);
    $check_vote_stmt->execute();
    $result_check = $check_vote_stmt->get_result();
    $user_vote_status = $result_check->fetch_assoc();
    $check_vote_stmt->close();

    if ($user_vote_status['has_voted'] == 1) {
        // User has already voted, so redirect with an error message
        header("Location: votingpage.php?vote_status=already_voted");
        exit();
    }

    // 2. Increment the candidate's vote count
    $update_votes_stmt = $conn->prepare("UPDATE votes SET vote_count = vote_count + 1 WHERE candidate = ?");
    $update_votes_stmt->bind_param("s", $candidate_name);
    $update_votes_stmt->execute();
    $update_votes_stmt->close();

    // 3. Mark the user as having voted
    $update_user_stmt = $conn->prepare("UPDATE users SET has_voted = 1 WHERE email = ?");
    $update_user_stmt->bind_param("s", $logged_in_user_id);
    $update_user_stmt->execute();
    $update_user_stmt->close();

    // Redirect with a success message
    header("Location: votingpage.php?vote_status=success");
    exit();
}

// Fetch all candidates for the voting page display.
$candidates = [];
$stmt_candidates = $conn->prepare("SELECT * FROM candidates");
$stmt_candidates->execute();
$result_candidates = $stmt_candidates->get_result();
while ($candidate = $result_candidates->fetch_assoc()) {
    $candidates[] = $candidate;
}
$stmt_candidates->close();

// Fetch the logged-in user's profile details.
$logged_in_user_id = $_SESSION['candidateId'];
$user_profile = null;
$user_has_voted = false; // Initialize the variable for HTML logic
$stmt_profile = $conn->prepare("SELECT username, email, voter_id, has_voted FROM users WHERE email = ?");
$stmt_profile->bind_param("s", $logged_in_user_id);
$stmt_profile->execute();
$result_profile = $stmt_profile->get_result();
if ($result_profile->num_rows > 0) {
    $user_profile = $result_profile->fetch_assoc();
    // Set the flag for the HTML logic
    $user_has_voted = ($user_profile['has_voted'] == 1);
}
$stmt_profile->close();
$conn->close();

$is_logged_in = isset($_SESSION['candidateId']);
$logged_in_user_email = $is_logged_in ? htmlspecialchars($_SESSION['candidateId']) : '';

// Display status message if redirected
$status_message = '';
if (isset($_GET['vote_status'])) {
    if ($_GET['vote_status'] == 'success') {
        $status_message = "Your vote has been cast successfully!";
    } elseif ($_GET['vote_status'] == 'already_voted') {
        $status_message = "You have already voted. You cannot vote again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Voting Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">College Election</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if ($is_logged_in): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Welcome, <?= $logged_in_user_email ?>!</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container my-5">
        <h1 class="text-center mb-4 text-primary">College Election 2025 - Vote Your Candidate</h1>
        
        <?php if ($status_message): ?>
            <div class="alert alert-info text-center">
                <?= htmlspecialchars($status_message) ?>
            </div>
        <?php endif; ?>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center">
            <?php foreach ($candidates as $candidate): ?>
                <div class="col">
                    <form method="POST" action="">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center">
                                <h5 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($candidate["position"]) ?></h5>
                                <h2 class="card-title fw-bold"><?= htmlspecialchars($candidate["name"]) ?></h2>
                                <p class="card-text"><?= htmlspecialchars($candidate["course"]) ?></p>
                                <input type="hidden" name="candidate_name" value="<?= htmlspecialchars($candidate["name"]) ?>">
                                <button class="btn btn-primary mt-3 w-100" type="submit" <?= $user_has_voted ? 'disabled' : '' ?>>
                                    Vote
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel">User Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($user_profile): ?>
                        <p><strong>Username:</strong> <?= htmlspecialchars($user_profile['username']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user_profile['email']) ?></p>
                        <p><strong>Voter ID:</strong> <?= htmlspecialchars($user_profile['voter_id']) ?></p>
                    <?php else: ?>
                        <p>Unable to retrieve profile information.</p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>