<?php
session_start(); 

function getDbCredentials($adv_no) {
    return ['dbname' => $adv_no, 'username' => 'root', 'password' => ''];
}

function connectDatabase($adv_no) {
    $credentials = getDbCredentials($adv_no);
    $host = "localhost";
    $username = $credentials['username'];
    $dbpassword = $credentials['password'];
    $dbname = $credentials['dbname'];

    $con = mysqli_connect($host, $username, $dbpassword, $dbname);

    if (!$con) {
        die("Connection failed! " . mysqli_connect_error());
    }

    return $con;
}

function getAdvertisementNumbers() {
    $adminHost = "localhost";
    $adminUsername = "root";
    $adminPassword = "";
    $adminDbname = "admin";

    $adminCon = mysqli_connect($adminHost, $adminUsername, $adminPassword, $adminDbname);

    if (!$adminCon) {
        die("Connection to admin database failed! " . mysqli_connect_error());
    }

    // Get current date
    $currentDate = date('Y-m-d');

    // Update query to include date filter
    $query = "SELECT advertisement FROM broadcasts WHERE '$currentDate' BETWEEN date_from AND date_to";
    $result = mysqli_query($adminCon, $query);

    $advertisements = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $advertisements[] = $row['advertisement'];
    }

    mysqli_close($adminCon);
    return $advertisements;
}

function getNextApplicationNumber($con, $adv_no) {
    $query = "SELECT application_no FROM register WHERE application_no LIKE '$adv_no%' ORDER BY application_no DESC LIMIT 1";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastAppNo = $row['application_no'];
        $lastNumber = substr($lastAppNo, -4); 
        $newNumber = intval($lastNumber) + 1; 
    } else {
        $newNumber = 1; 
    }

    return $adv_no . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
}

function insertUser($adv_no, $email, $phone, $name, $password) {
    $con = connectDatabase($adv_no);

    $application_no = getNextApplicationNumber($con, $adv_no);
    $registration_date = date('Y-m-d H:i:s'); 

    $stmt = $con->prepare("INSERT INTO register (adv_no, email, phone, name, password, application_no, registration_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $adv_no, $email, $phone, $name, $password, $application_no, $registration_date);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Entries added! Your Application Number is: " . $application_no;
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    mysqli_close($con);
}

if (isset($_POST['submit'])) {
    $adv_no = $_POST['adv_no'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $name = $_POST['name'];
    $password = $_POST['password'];

    insertUser($adv_no, $email, $phone, $name, $password);
    
    header("Location: register.php");
    exit();
}

$advertisementNumbers = getAdvertisementNumbers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form - CEERI PILANI Website</title>
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
            padding-top: 70px; 
        }

        .message {
            text-align: center;
            font-size: 14px;
            color: green;
            margin-top: 10px;
        }

        .inner-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px; 
            margin-top: 100px;
        }

        .inner-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }

        .form-section {
            margin-bottom: 15px;
        }

        .form-section label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-section input[type="text"],
        .form-section input[type="email"],
        .form-section input[type="tel"],
        .form-section input[type="password"],
        .form-section select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-section button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .form-section button:hover {
            background-color: #45a049;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
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
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="message"><?php echo $_SESSION['message']; ?></div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>
                <form action="register.php" method="POST" id="registerForm">
                    <div class="inner-container">
                        <h2>Registration Page</h2>
                        <div class="form-section">
                            <label for="adv_no">Advertisement No:</label>
                            <select id="adv_no" name="adv_no" required>
                                <?php foreach ($advertisementNumbers as $adv_no): ?>
                                    <option value="<?php echo htmlspecialchars($adv_no); ?>"><?php echo htmlspecialchars($adv_no); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-section">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-section">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        <div class="form-section">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-section">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-section">
                            <button type="submit" name="submit">Register</button>
                        </div>
                        <p>Already registered? <a href="login.php">Login here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>
