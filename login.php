<?php
session_start();
include('connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adv_no = $_POST['adv_no'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $con = connectDatabase($adv_no);

    // Fetch user details including the application_no, is_finalized, and form_accessed
    $query = "SELECT User_id, email, password, application_no, is_finalized, form_accessed FROM register WHERE email=? AND password=? LIMIT 1";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $stmt->bind_result($User_id, $email, $password, $application_no, $is_finalized, $form_accessed);
    $stmt->store_result();

    if ($stmt->fetch()) {
        $_SESSION['login_user_id'] = $User_id;
        $_SESSION['login_user_email'] = $email;
        $_SESSION['application_no'] = $application_no; // Store application_no in session
        $_SESSION['dbbnaam'] = $adv_no; // for dbname in session var

        // Redirect based on the finalized status and form_accessed
        if ($is_finalized == 1) {
            header("Location: printdoc.php");
        } else {
            if ($form_accessed == 1) {
                header("Location: persdet.php");
            } else {
                header("Location: postselection.php");
            }
        }
        exit();
    } else {
        $error = "Username or Password is invalid";
        $_SESSION['message'] = $error;
    }

    mysqli_close($con);
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = "localhost"; // Replace with your database server name
$username = "root";        // Replace with your database username
$password = "";            // Replace with your database password
$dbname = "admin";         // The name of the database to connect to

$scrollContent = '';
$advOptions = '';

// Fetch advertisement data from the 'broadcasts' table
$adminConn = new mysqli($servername, $username, $password, $dbname);

if ($adminConn->connect_error) {
    die("Connection failed: " . $adminConn->connect_error);
}

// Fetch advertisements for the scroll content
$sqlScroll = "SELECT advertisement, adv_title FROM broadcasts";
$resultScroll = $adminConn->query($sqlScroll);

if ($resultScroll->num_rows > 0) {
    while ($row = $resultScroll->fetch_assoc()) {
        $advertisement = $row['advertisement'];
        $adv_title = $row['adv_title']; // Fetch the advertisement title
        $scrollContent .= '<a href="#" class="scroll-link" data-adv="' . htmlspecialchars($advertisement) . '">'
                        . htmlspecialchars($advertisement) . ' - ' . htmlspecialchars($adv_title) . '</a>';
    }
}

// Fetch advertisement numbers for the dropdown list
$sqlDropdown = "SELECT advertisement FROM broadcasts";
$resultDropdown = $adminConn->query($sqlDropdown);

if ($resultDropdown->num_rows > 0) {
    while ($row = $resultDropdown->fetch_assoc()) {
        $advertisement = $row['advertisement'];
        $advOptions .= '<option value="' . htmlspecialchars($advertisement) . '">' . htmlspecialchars($advertisement) . '</option>';
    }
}

$adminConn->close();
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

        .login-container, .scroll-panel {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .login-container {
            height: 70vh; /* Adjust based on your needs */
        }

        .scroll-panel {
            height: 70vh; /* Adjust based on your needs */
            overflow: hidden; /* Hide overflow */
            position: relative; /* For proper positioning of the scroll content */
        }

        .scroll-content {
            position: absolute;
            top: 100%; /* Start the content from below the visible area */
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden; /* Hide any content overflow */
            animation: scroll-up 20s linear infinite; /* Scroll animation */
        }

        .scroll-content a {
            display: block;
            padding: 8px 16px;
            text-decoration: none;
            color: #333;
        }

        .scroll-content a:hover {
            background-color: #f0f0f0;
        }

        /* Animation for scrolling content from bottom to top */
        @keyframes scroll-up {
            0% { transform: translateY(0); }
            100% { transform: translateY(-100%); }
        }

        .register-link {
            margin-top: 20px;
            text-align: center;
        }

        .register-link a {
            color: #007bff;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <div class="left-half">
            <div class="login-container">
                <h2>Login Page</h2>
                <p>Please login with your credentials:</p>
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="adv_no">Advertisement No:</label>
                        <select id="adv_no" name="adv_no" required class="form-control">
                            <?php echo $advOptions; ?>
                        </select>
                    </div>
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
                <div class="register-link">
                    <p>Not registered yet? <a href="register.php">Register here</a></p>
                </div>
                <?php if (isset($_SESSION['message'])): ?>
                    <p class="message"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="right-half">
            <div class="scroll-panel">
                <div class="scroll-content">
                    <?php echo $scrollContent; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('.scroll-link');

            links.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const advertisement = this.getAttribute('data-adv');

                    fetch('fetch_document.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'advertisement=' + encodeURIComponent(advertisement)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.path;
                        } else {
                            alert('Document not found.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred.');
                    });
                });
            });
        });
    </script>
</body>
</html>
