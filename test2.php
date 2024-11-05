<?php
session_start();

 $educationHierarchy = ['10th', '12th', 'graduation', 'postgraduation'];

 $highestEducation = isset($_SESSION['highest_education']) ? $_SESSION['highest_education'] : '';

 $educationTypesToShow = [];
foreach ($educationHierarchy as $eduType) {
    if ($eduType == $highestEducation || array_search($eduType, $educationHierarchy) < array_search($highestEducation, $educationHierarchy)) {
        $educationTypesToShow[] = $eduType;
    }
}

 $educationTypesJson = json_encode($educationTypesToShow);

 function deleteUserEducation($id, $education_type) {
    $host = "localhost";
    $username = "root";
    $dbpassword = ""; // Fill in your database password
    $dbname = $_SESSION['dbbnaam']; // Your database name
    
    $con = mysqli_connect($host, $username, $dbpassword, $dbname);

    if (!$con) {
        die(json_encode(["status" => "error", "message" => "Connection failed! " . mysqli_connect_error()]));
    }

    // Get the file path before deletion
    $stmt = $con->prepare("SELECT document_path FROM education_details WHERE id = ? AND education_type = ?");
    $stmt->bind_param("ss", $id, $education_type);
    $stmt->execute();
    $result = $stmt->get_result();
    $filePath = $result->fetch_assoc()['document_path'];

    // Delete the record from the database
    $stmt = $con->prepare("DELETE FROM education_details WHERE id = ? AND education_type = ?");
    $stmt->bind_param("ss", $id, $education_type);

    if ($stmt->execute()) {
        // Delete the file from the server
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
    mysqli_close($con);
}

// Function to insert or update user data in the database
function insertUser($id, $eduType, $schoolName, $percentage, $board, $documentPath) {
    $host = "localhost";
    $username = "root";
    $dbpassword = ""; // Fill in your database password
    $dbname = $_SESSION['dbbnaam']; // Your database name

    $con = mysqli_connect($host, $username, $dbpassword, $dbname);

    if (!$con) {
        die(json_encode(["status" => "error", "message" => "Connection failed! " . mysqli_connect_error()]));
    }

    // Check if record exists for the user ID and education type
    $stmt = $con->prepare("SELECT * FROM education_details WHERE id = ? AND education_type = ?");
    $stmt->bind_param("ss", $id, $eduType);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the existing record
        $stmt = $con->prepare("UPDATE education_details SET 
            school_name = ?, percentage = ?, board = ?, document_path = ? 
            WHERE id = ? AND education_type = ?");
        $stmt->bind_param("ssssss", $schoolName, $percentage, $board, $documentPath, $id, $eduType);
    } else {
        // Insert a new record
        $stmt = $con->prepare("INSERT INTO education_details (
            id, education_type, school_name, percentage, board, document_path 
        ) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $id, $eduType, $schoolName, $percentage, $board, $documentPath);
    }

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "id" => $id,
            "education_type" => $eduType,
            "school_name" => $schoolName,
            "percentage" => $percentage,
            "board" => $board,
            "document_path" => $documentPath
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
    mysqli_close($con);
}

function fetchEducationDetails($userId) {
    $host = "localhost";
    $username = "root";
    $dbpassword = ""; // Fill in your database password
    $dbname = $_SESSION['dbbnaam']; // Your database name

    $con = mysqli_connect($host, $username, $dbpassword, $dbname);

    if (!$con) {
        return [];
    }

    $stmt = $con->prepare("SELECT * FROM education_details WHERE id = ?");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();
    mysqli_close($con);

    return $data;
}

// Process POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['formType']) && $_POST['formType'] == 'addEducation') {
        $eduType = $_POST['secondary_edu'];
        $schoolName = $_POST['secondary_school_name'];
        $percentage = $_POST['secondary_percentage'];
        $board = $_POST['secondary_board'];
        $id = $_SESSION['login_user_id'];

        // Handle file upload
        $documentPath = '';
        if (isset($_FILES['secondary_document']) && $_FILES['secondary_document']['error'] == 0) {
            $targetDir = "uploads/";
            $documentPath = $targetDir . basename($_FILES["secondary_document"]["name"]);

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            if (move_uploaded_file($_FILES["secondary_document"]["tmp_name"], $documentPath)) {
                // File uploaded successfully
            } else {
                die(json_encode(["status" => "error", "message" => "Failed to upload document."]));
            }
        } else {
            die(json_encode(["status" => "error", "message" => "No document uploaded or upload error."]));
        }

        insertUser($id, $eduType, $schoolName, $percentage, $board, $documentPath);
        exit;
    } else if (isset($_POST['id']) && isset($_POST['education_type'])) {
        $id = $_POST['id'];
        $education_type = $_POST['education_type'];
        deleteUserEducation($id, $education_type);
        exit;
    }

    if (isset($_POST['next'])) {
        $_SESSION['isNextButtonEnabled'] = true;
    }
}

$userId = $_SESSION['login_user_id'];
$educationDetails = fetchEducationDetails($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Education Qualification Form</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
        
         

        .content {
    background-color: #ffffff; /* Sets the background color to white */
    margin-left: 200px;        /* Adds a left margin of 200px */
    margin-top: 70px;          /* Adds a top margin of 20px */
    padding: 20px;             /* Adds padding of 20px on all sides */
    flex: 1;                   /* Allows the element to grow and shrink as needed in a flex container */
}


        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            height: 100vh;
            overflow-y: auto;
        }
        #nextButton {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            margin-left: auto;
        }

        #nextButton:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .form-section {
            margin-top: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            
        }

        .form-section label {
            display: inline-block;
            width: 200px;
            text-align: right;
            margin-right: 10px;
        }

        .form-section input[type=text],
        .form-section input[type=number],
        .form-section input[type=file],
        .form-section select {
            width: calc(100% - 210px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-section input[type=button],
        .form-section input[type=submit] {
            width: auto;
            margin-left: 210px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-section input[type=button]:hover,
        .form-section input[type=submit]:hover {
            background-color: #45a049;
        }

        #category_section,
        #disability_section {
            display: none;
        }

        .add-more,
        .remove-button {
            background-color: #0d416b;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 20px;
            width: 30px;
            height: 30px;
            margin-left: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .remove-button {
            background-color: #0d416b;
        }
    </style>
</head>
<body>
<?php include('navbar2.php'); ?>
<?php include('navnew.php'); ?>
 
 

<div class="content">
    <div class="container">
        <div class="section" id="initialSection">
            <div class="section-header">Education Qualification Form</div>
            <form id="eduForm" enctype="multipart/form-data" method="POST">
                <div class="form-section">
                    <label for="secondary_edu_0">Education Type:</label>
                    <select id="secondary_edu_0" name="secondary_edu" required>
                        <!-- Options will be populated by JavaScript -->
                    </select>
                </div>
                <div class="form-section">
                    <label for="secondary_school_name_0">School/Institute Name:</label>
                    <input type="text" id="secondary_school_name_0" name="secondary_school_name" required>
                </div>
                <div class="form-section">
                    <label for="secondary_percentage_0">Percentage of Marks:</label>
                    <input type="number" id="secondary_percentage_0" name="secondary_percentage" step="0.01" required>
                </div>
                <div class="form-section">
                    <label for="secondary_board_0">Board:</label>
                    <input type="text" id="secondary_board_0" name="secondary_board" required>
                </div>
                <div class="form-section">
                    <label for="secondary_document_0">Upload Document:</label>
                    <input type="file" id="secondary_document_0" name="secondary_document" required>
                </div>
                <div class="form-section">
                    <input type="hidden" name="formType" value="addEducation">
                    <input type="submit" value="+" class="add-more" id="addMoreButton">
                </div>
            </form>

            <div id="qualificationTable">
    <h3>Added Qualifications</h3>
    <table id="eduTable" class="table table-striped">
        <thead>
        <tr>
            <th>Education Type</th>
            <th>School/Institute Name</th>
            <th>Percentage of Marks</th>
            <th>Board</th>
            <th>Document</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="eduTableBody">
        <!-- Existing rows will be dynamically added here -->
        <?php 
        $existingEducationTypes = [];
        foreach ($educationDetails as $detail) : 
            $existingEducationTypes[] = $detail['education_type'];
        ?>
            <tr>
                <td><?= htmlspecialchars($detail['education_type']) ?></td>
                <td><?= htmlspecialchars($detail['school_name']) ?></td>
                <td><?= htmlspecialchars($detail['percentage']) ?></td>
                <td><?= htmlspecialchars($detail['board']) ?></td>
                <td><a href="<?= htmlspecialchars($detail['document_path']) ?>" target="_blank">View Document</a></td>
                <td>
                    <button type="button" class="remove-button" 
                            onclick="removeTableRow(this, '<?= htmlspecialchars($detail['id']) ?>', '<?= htmlspecialchars($detail['education_type']) ?>')">-</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<input type="hidden" id="existingEducationTypes" value="<?= htmlspecialchars(json_encode($existingEducationTypes)) ?>">


            <div class="form-section">
                <input type="button" value="Back" onclick="location.href='persdet.php'">
                <button id="nextButton" disabled>Next</button>
                </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Array of education types to show, passed from PHP
    const educationTypesToShow = <?php echo $educationTypesJson; ?>;
    const eduTypeSelect = document.getElementById('secondary_edu_0');
    const allEducationTypes = ['10th', '12th', 'graduation', 'postgraduation'];
    const existingEducationTypes = JSON.parse(document.getElementById('existingEducationTypes').value || '[]');

    // Maps education types to the required number of rows
    const educationRowThresholds = {
        '10th': 1,
        '12th': 2,
        'graduation': 3,
        'postgraduation': 4
    };

    // Determine the required number of rows based on the highest education level
    const highestEducation = "<?php echo $highestEducation; ?>";
    const requiredRows = educationRowThresholds[highestEducation] || 0;

    // Populate the dropdown based on educationTypesToShow
    educationTypesToShow.forEach(type => {
        if (allEducationTypes.includes(type)) {
            const option = document.createElement('option');
            option.value = type;
            option.textContent = type;
            eduTypeSelect.appendChild(option);
        }
    });

    // Remove existing education types from the dropdown
    existingEducationTypes.forEach(type => {
        const option = eduTypeSelect.querySelector(`option[value="${type}"]`);
        if (option) {
            option.remove();
        }
    });

    // Function to check the number of rows and enable the "Next" button if required rows are met
    function checkRowCount() {
        const rowCount = document.getElementById('eduTableBody').rows.length;
        const nextButton = document.getElementById('nextButton');
        if (rowCount >= requiredRows) {
            nextButton.disabled = false;
        } else {
            nextButton.disabled = true;
        }
    }

    // Initial check after loading existing rows
    checkRowCount();

    // Handle form submission via AJAX
    document.getElementById('eduForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    // Add new row to the table
                    const tableBody = document.getElementById('eduTableBody');
                    const newRow = document.createElement('tr');

                    newRow.innerHTML = `
                        <td>${response.education_type}</td>
                        <td>${response.school_name}</td>
                        <td>${response.percentage}</td>
                        <td>${response.board}</td>
                        <td><a href="${response.document_path}" target="_blank">View Document</a></td>
                        <td>
                            <button type="button" class="remove-button" 
                                    onclick="removeTableRow(this, '${response.id}', '${response.education_type}')">-</button>
                        </td>
                    `;

                    tableBody.appendChild(newRow);

                    // Remove the added education type from the dropdown
                    const option = eduTypeSelect.querySelector(`option[value="${response.education_type}"]`);
                    if (option) {
                        option.remove();
                    }

                    // Reset form fields
                    document.getElementById('eduForm').reset();

                    // Check row count after adding a new row
                    checkRowCount();
                } else {
                    alert(`Error adding data: ${response.message}`);
                }
            } else {
                alert(`Error adding data: ${xhr.statusText}`);
            }
        };
        xhr.send(formData);
    });

    // Function to remove education row
    window.removeTableRow = function(button, id, educationType) {
        const rowToRemove = button.closest('tr');

        // AJAX call to delete data from the database
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    // On success, remove row from table
                    rowToRemove.remove();

                    // Add the education type back to the dropdown
                    const newOption = document.createElement('option');
                    newOption.value = educationType;
                    newOption.textContent = educationType;
                    eduTypeSelect.appendChild(newOption);

                    // Check row count after removing a row
                    checkRowCount();
                } else {
                    alert(`Error deleting data: ${response.message}`);
                }
            } else {
                alert(`Error deleting data: ${xhr.statusText}`);
            }
        };

        const formData = `id=${id}&education_type=${educationType}`;
        xhr.send(formData);
    };
});
</script>


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
 