<?php
// Function to get database credentials
function getDbCredentials($adv_no) {
    return [
        'username' => 'root',
        'password' => '',
        'dbname' => $adv_no
    ];
}

// Function to connect to the database
function connectDatabase($dbname) {
    $host = "localhost";
    $username = 'root';
    $dbpassword = '';
    $dbname = $dbname;

    $con = mysqli_connect($host, $username, $dbpassword, $dbname);

    if (!$con) {
        die("Connection failed! " . mysqli_connect_error());
    }

    return $con;
}

// Function to fetch applications from the database
function fetchApplicationsFromDb($dbname) {
    $conn = connectDatabase($dbname);
    $fields = [
        'full_name', 'application_no', 'email', 'phone',
        'dob', 'nationality', 'ex_serviceman', 'differently_abled'
    ];
    $rows = [];

    // Fetch data from register table where shortlisted = 1
    $sql = "SELECT User_id, email, phone, application_no, shortlisted FROM register WHERE shortlisted = 1";
    $registerResult = $conn->query($sql);

    while ($registerRow = $registerResult->fetch_assoc()) {
        $userId = $registerRow['User_id'];
        $values = [
            'application_no' => $registerRow['application_no'],
            'email' => $registerRow['email'],
            'phone' => $registerRow['phone'],
            'shortlisted' => $registerRow['shortlisted'],
            'user_id' => $registerRow['User_id']
        ];

        // Fetch data from personal_details table by matching User_id with id
        $sql = "SELECT full_name, dob, nationality, ex_serviceman, differently_abled 
                FROM personal_details 
                WHERE id = '$userId'";
        $personalResult = $conn->query($sql);

        if ($personalRow = $personalResult->fetch_assoc()) {
            $values['full_name'] = $personalRow['full_name'];
            $values['dob'] = $personalRow['dob'];
            $values['nationality'] = $personalRow['nationality'];
            $values['ex_serviceman'] = $personalRow['ex_serviceman'];
            $values['differently_abled'] = $personalRow['differently_abled'];
        } else {
            $values['full_name'] = 'No data';
            $values['dob'] = 'No data';
            $values['nationality'] = 'No data';
            $values['ex_serviceman'] = 'No data';
            $values['differently_abled'] = 'No data';
        }

        $rows[] = $values;
    }

    $conn->close();

    return [$fields, $rows];
}

// Function to update the shortlisted status
function updateShortlistedStatus($dbname, $shortlistedCandidates) {
    $conn = connectDatabase($dbname);

    // Update the shortlisted status to 0 for selected candidates
    foreach ($shortlistedCandidates as $userId) {
        $sql = "UPDATE register SET shortlisted = 0 WHERE User_id = '$userId'";
        $conn->query($sql);
    }

    $conn->close();
}

if (isset($_GET['adv'])) {
    $selectedDb = $_GET['adv'];
    list($fields, $rows) = fetchApplicationsFromDb($selectedDb);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $shortlistedCandidates = isset($_POST['shortlisted']) ? $_POST['shortlisted'] : [];
        updateShortlistedStatus($selectedDb, $shortlistedCandidates);
        
        // Redirect to view_shortlisted_applications.php after successful update
        header("Location: view_shortlisted_applications.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 20px;
        }
        .container {
            max-width: 1000px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 6%;

        }
        .form-section input[type=button]  {
            width: auto;
            margin-left: 1px;
            margin-top: 5px;

            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #343a40;
            font-weight: bold;
        }
        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        th {
            background-color: #007bff;
            color: #ffffff;
            text-align: center;
        }
        td {
            background-color: #f9f9f9;
            color: #495057;
            text-align: center;
        }
        .checkbox-column {
            text-align: center;
        }
        .btn-primary {
            width: 100%;
            padding: 10px;
            font-size: 18px;
            border-radius: 5px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            margin-top: 20px;
        }
        @media (max-width: 768px) {
            th, td {
                padding: 10px;
                font-size: 14px;
            }
            .btn-primary {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
<?php include('navnew.php'); ?>

    <div class="container">
        <?php if (isset($fields) && isset($rows)) { ?>
            <h2>Applicant Details for <?php echo htmlspecialchars($selectedDb); ?></h2>
            <form method="POST">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="checkbox-column">Disqualify?</th>
                            <?php foreach ($fields as $field) { ?>
                                <th><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $field))); ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row) { ?>
                            <tr>
                                <td class="checkbox-column">
                                    <input type="checkbox" name="shortlisted[]" value="<?php echo htmlspecialchars($row['user_id']); ?>">
                                </td>
                                <?php foreach ($fields as $field) { ?>
                                    <td><?php echo htmlspecialchars($row[$field]); ?></td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary">Submit</button>
                <div class="form-section">
                <input type="button" value="Back" onclick="location.href='view_shortlisted_applications.php'">
                 </div>
            </form>
        <?php } else { ?>
            <p>No data found for the selected advertisement.</p>
        <?php } ?>
        
    </div>
    
</body>
</html>