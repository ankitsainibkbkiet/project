<?php
session_start();
$host = "localhost";
$username = "root";
$dbpassword = ""; // Fill in your database password
$dbname = $_SESSION['dbbnaam']; // Your database name

// Create connection
$conn = new mysqli($host, $username, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Use prepared statements to prevent SQL injection
$login_user_id = $_SESSION['login_user_id'];

// Query the database for personal details
$sql = "SELECT * FROM personal_details WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $login_user_id);
$stmt->execute();
$result = $stmt->get_result();

// Query the database for education details
$adi = "SELECT * FROM education_details WHERE id = ?";
$stmt2 = $conn->prepare($adi);
$stmt2->bind_param("i", $login_user_id);
$stmt2->execute();
$adii = $stmt2->get_result();


// Query the database for application number
$app_sql = "SELECT application_no FROM register WHERE User_id = ?";
$stmt5 = $conn->prepare($app_sql);
$stmt5->bind_param("i", $login_user_id);
$stmt5->execute();
$app_result = $stmt5->get_result();
$app_row = $app_result->fetch_assoc();

// Query the database for experience details
$qq = "SELECT * FROM experience_details WHERE id = ?";
$stmt3 = $conn->prepare($qq);
$stmt3->bind_param("i", $login_user_id);
$stmt3->execute();
$aad = $stmt3->get_result();

// Query the database for user documents
$doc_sql = "SELECT * FROM user_documents WHERE id = ?";
$stmt4 = $conn->prepare($doc_sql);
$stmt4->bind_param("i", $login_user_id);
$stmt4->execute();
$doc_result = $stmt4->get_result();
$doc_row = $doc_result->fetch_assoc();

// Handle AJAX request to finalize application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'finalize') {
    $update_sql = "UPDATE register SET is_finalized = 1 WHERE User_id = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("i", $login_user_id);
    if ($stmt_update->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt_update->error]);
    }
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Printable Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 210mm; /* A4 width */
            margin: 0 auto;
        }

        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            margin-top: 70px;
            margin-left: 50%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            height: 100vh;
        } 

        .container {
            width: 100%;
            max-width: 210mm; /* A4 width */
            padding: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        .header {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .print-button, .finalize-button {
            background-color: #4CAF50; /* Green */
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 20px 0;
            cursor: pointer;
            border-radius: 5px;
        }

        .finalize-button {
            background-color: #f44336; /* Red */
        }

        @media print {
            @page { margin: 1cm; size: A4; }
            body { margin: 1cm; }
            
            /* Hide elements that should not appear in print */
            .print-button,
            .finalize-button,
            nav,
            .navbar2,
            .navnew {
                display: none;
            }

            /* Ensure the form container is displayed */
            .form-container {
                height: auto;
                margin: 0;
                box-shadow: none;
            }
        }

        h1, h2 {
            text-align: center;
        }

        .image-cell {
            width: 120px;
            text-align: center;
        }

        .image-cell img {
            max-width: 100%;
            height: auto;
        }
    </style>
    
</head>
<body>
 <?php include('lastnav.php'); ?>

<div class="form-container">
    <div class="container">
        <h1>Candidate Details</h1>
        <h2>Online Application No. <?php echo isset($app_row['application_no']) ? htmlspecialchars($app_row['application_no']) : 'Not Found'; ?></h2>

        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<table>';
                echo '<tr><td class="header image-cell"><img src="'. $doc_row['photo_path'] .'" alt="Photo"></td><td class="header image-cell"><img src="'. $doc_row['signature_path'] .'" alt="Signature"></td></tr>';
                echo '</table>';
                echo '<table>';
                echo '<tr><td class="header">Full Name: '. $row['full_name'] . '</td><td class="header">DOB: '. $row['dob'] .'</td><td class="header">Registered Mobile Number: '. $row['registered_mobile'] .'</td></tr>';
                echo '<tr><td class="header">Registered Email: '. $row['registered_email'] .'</td><td class="header">Aadhaar Number: '. $row['aadhaar_no'] .'</td><td class="header">Gender: '.  $row['gender'] .'</td></tr>';
                echo '<tr><td class="header">Marital Status: '. $row['marital_status'] . '</td><td class="header">Nationality: '. $row['nationality'] .'</td><td class="header">State: '. $row['state'] .'</td></tr>';
                echo '<tr><td class="header">Mother\'s Name: '. $row['mothers_name'] .'</td><td class="header">Father\'s Name: '. $row['fathers_name'] .'</td><td class="header">Alternate Mobile Number: '.  $row['alternate_mobile'] .'</td></tr>';
                echo '<tr><td class="header">Category: '. $row['category'] . '</td><td class="header">Caste Category Certificate: <a href="'. $row['category_certificate'] .'" target="_blank">View</a></td><td class="header">EX-Serviceman: '. $row['ex_serviceman'] . '</td></tr>';
                echo '<tr><td class="header">Specify EX-Serviceman: '. $row['specify_ex_serviceman'] . '</td><td class="header">Date of Retirement: '. $row['date_of_retirement'] . '</td><td class="header">Service No: '. $row['service_no'] . '</td></tr>';
                echo '<tr><td class="header">Differently Abled: '. $row['differently_abled'] . '</td><td class="header">Disability Percentage: '. $row['disability_percentage'] . '</td><td class="header">Disability Type: '. $row['disability_type'] . '</td></tr>';
                echo '<tr><td class="header">Disability Certificate: <a href="'. $row['disability_certificate'] .'" target="_blank">View</a></td><td class="header">Correspondence Address: '. $row['corrospondance_address'] . '</td><td class="header">Permanent Address: '. $row['permanent_address'] . '</td></tr>';
                echo '</table>';
                echo '<table>';
                echo '<tr><td class="header" colspan="2">Correspondence City Name: '. $row['corrospondance_city'] . '</td><td class="header" colspan="2">Permanent City Name: '. $row['permanent_city'] . '</td></tr>';
                echo '</table>';
            }
        } else {
            echo "<p>No personal details found.</p>";
        }
        ?>

        <?php
        if ($adii->num_rows > 0) {
            echo '<h2>Education Details</h2>';
            echo '<table>';
            echo '<tr><th>Education Type</th><th>School Name</th><th>Percentage</th><th>Board</th><th>Document</th></tr>';
            while($roww = $adii->fetch_assoc()) {
                echo '<tr><td>'. $roww['education_type'] .'</td><td>'. $roww['school_name'] .'</td><td>'. $roww['percentage'] .'</td><td>'. $roww['board'] .'</td><td><a href="'. $roww['document_path'] .'" target="_blank">View</a></td></tr>';
            }
            echo '</table>';
        }
        ?>

        <?php
        if ($aad->num_rows > 0) {
            echo '<h2>Experience Details</h2>';
            echo '<table>';
            echo '<tr><th>Experience Title</th><th>Company Name</th><th>Duration From</th><th>Duration To</th><th>Document</th></tr>';
            while($rowe = $aad->fetch_assoc()) {
                echo '<tr><td>'. $rowe['experience_title'] .'</td><td>'. $rowe['company_name'] .'</td><td>'. $rowe['duration_from'] .'</td><td>'. $rowe['duration_to'] .'</td><td><a href="'. $rowe['document_path'] .'" target="_blank">View</a></td></tr>';
            }
            echo '</table>';
        }
        ?>

        <button class="print-button" onclick="window.print()">Print Now ! </button>
     </div>
</div>
</body>
</html>
