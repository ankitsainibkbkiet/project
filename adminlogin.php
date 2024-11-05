<?php
session_start(); // Start session to use session variables

// Database connection
$host = 'localhost'; // Database host
$dbname = 'admin'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password

// Create a connection using mysqli
$con = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$con) {
    die(json_encode(["status" => "error", "message" => "Connection failed! " . mysqli_connect_error()]));
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the query
    $stmt = $con->prepare('SELECT * FROM loginid WHERE email = ? AND password = ?');
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if the user exists
    if ($result->num_rows > 0) {
        // Credentials are correct
        $_SESSION['user'] = $email; // Store user info in session
        header('Location: adminhomepage.php'); // Redirect to admin homepage
        exit();
    } else {
        // Invalid credentials
        $_SESSION['message'] = 'Invalid email or password.';
        header('Location: adminlogin.php'); // Redirect back to login page
        exit();
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$con->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page - CEERI PILANI Website</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2014/06/16/23/40/grey-370125_640.png');
            background-size: cover;
            background-position: center;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 70px; /* Adjust to fit the navbar height */
        }
        .message {
            color: red;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 80%;
            max-width: 1200px;
            margin: 0 auto;
        }
        .left-half, .right-half {
            width: calc(50% - 20px);
        }
        .login-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            height: 70vh; /* Adjust based on your needs */
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <div class="left-half">
            <div class="login-container">
                <h2>Administrator Login Page</h2>
                <p>Please login with your credentials:</p>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
                
                <?php if (isset($_SESSION['message'])): ?>
                    <p class="message"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
