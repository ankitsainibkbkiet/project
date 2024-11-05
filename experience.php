<?php
session_start();

// Function to delete a user experience record from the database
function deleteUserExperience($id, $experience_title) {
    $host = "localhost";
    $username = "root";
    $dbpassword = ""; // Fill in your database password
    $dbname = $_SESSION['dbbnaam']; // Your database name
    
    $con = mysqli_connect($host, $username, $dbpassword, $dbname);

    if (!$con) {
        die(json_encode(["status" => "error", "message" => "Connection failed! " . mysqli_connect_error()]));
    }

    // Get the file path before deletion
    $stmt = $con->prepare("SELECT document_path FROM experience_details WHERE id = ? AND experience_title = ?");
    $stmt->bind_param("ss", $id, $experience_title);
    $stmt->execute();
    $result = $stmt->get_result();
    $filePath = $result->fetch_assoc()['document_path'];

    // Delete the record from the database
    $stmt = $con->prepare("DELETE FROM experience_details WHERE id = ? AND experience_title = ?");
    $stmt->bind_param("ss", $id, $experience_title);

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

// Function to insert or update user experience data in the database
function insertExperience($id, $title, $company, $durationFrom, $durationTo, $documentPath) {
    $host = "localhost";
    $username = "root";
    $dbpassword = ""; // Fill in your database password
    $dbname = $_SESSION['dbbnaam']; // Your database name

    $con = mysqli_connect($host, $username, $dbpassword, $dbname);

    if (!$con) {
        die(json_encode(["status" => "error", "message" => "Connection failed! " . mysqli_connect_error()]));
    }

    // Check if record exists for the user ID and experience title
    $stmt = $con->prepare("SELECT * FROM experience_details WHERE id = ? AND experience_title = ?");
    $stmt->bind_param("ss", $id, $title);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the existing record
        $stmt = $con->prepare("UPDATE experience_details SET 
            company_name = ?, duration_from = ?, duration_to = ?, document_path = ? 
            WHERE id = ? AND experience_title = ?");
        $stmt->bind_param("ssssss", $company, $durationFrom, $durationTo, $documentPath, $id, $title);
    } else {
        // Insert a new record
        $stmt = $con->prepare("INSERT INTO experience_details (
            id, experience_title, company_name, duration_from, duration_to, document_path 
        ) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $id, $title, $company, $durationFrom, $durationTo, $documentPath);
    }

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "id" => $id,
            "experience_title" => $title,
            "company_name" => $company,
            "duration_from" => $durationFrom,
            "duration_to" => $durationTo,
            "document_path" => $documentPath
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
    mysqli_close($con);
}

function fetchExperienceDetails($userId) {
    $host = "localhost";
    $username = "root";
    $dbpassword = ""; // Fill in your database password
    $dbname = $_SESSION['dbbnaam']; // Your database name

    $con = mysqli_connect($host, $username, $dbpassword, $dbname);

    if (!$con) {
        return [];
    }

    $stmt = $con->prepare("SELECT * FROM experience_details WHERE id = ?");
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
    if (isset($_POST['formType']) && $_POST['formType'] == 'addExperience') {
        $title = $_POST['experience_title'];
        $company = $_POST['company_name'];
        $durationFrom = $_POST['duration_from'];
        $durationTo = $_POST['duration_to'];
        $id = $_SESSION['login_user_id'];

        // Handle file upload
        $documentPath = '';
        if (isset($_FILES['experience_document']) && $_FILES['experience_document']['error'] == 0) {
            $targetDir = "uploads/";
            $documentPath = $targetDir . basename($_FILES["experience_document"]["name"]);

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            if (move_uploaded_file($_FILES["experience_document"]["tmp_name"], $documentPath)) {
                // File uploaded successfully
            } else {
                die(json_encode(["status" => "error", "message" => "Failed to upload document."]));
            }
        } else {
            die(json_encode(["status" => "error", "message" => "No document uploaded or upload error."]));
        }

        insertExperience($id, $title, $company, $durationFrom, $durationTo, $documentPath);
        exit;
    } else if (isset($_POST['id']) && isset($_POST['experience_title'])) {
        $id = $_POST['id'];
        $experience_title = $_POST['experience_title'];
        deleteUserExperience($id, $experience_title);
        exit;
    }
}

$userId = $_SESSION['login_user_id'];
$experienceDetails = fetchExperienceDetails($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Experience Form</title>
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
            background-color: #ffffff;
            margin-left: 200px;
            margin-top: 70px;
            padding: 20px;
            flex: 1;
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

        .section-header {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .form-section {
            margin-bottom: 20px;
        }

        .form-section label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-section input[type=text],
        .form-section input[type=date],
        .form-section input[type=file] {
            width: calc(100% - 20px);
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
            <div class="section-header">Experience Form</div>
            <form id="experienceForm" enctype="multipart/form-data" method="POST">
                <div class="form-section">
                    <label for="experience_title_0">Job Title / Job Profile :</label>
                    <input type="text" id="experience_title_0" name="experience_title" required>
                </div>
                <div class="form-section">
                    <label for="company_name_0">Company Name:</label>
                    <input type="text" id="company_name_0" name="company_name" required>
                </div>
                <div class="form-section">
                    <label for="duration_from_0">Duration From:</label>
                    <input type="date" id="duration_from_0" name="duration_from" required>
                </div>
                <div class="form-section">
                    <label for="duration_to_0">Duration To:</label>
                    <input type="date" id="duration_to_0" name="duration_to" required>
                </div>
                <div class="form-section">
                    <label for="experience_document_0">Upload Offer Letter / ID Card / Any Relevant Document :</label>
                    <input type="file" id="experience_document_0" name="experience_document" required>
                </div>

                <div class="form-section">
                    <input type="hidden" name="formType" value="addExperience">
                    <input type="submit" value="+" class="add-more" id="addMoreButton">
                </div>
                
            </form>

            <div id="experienceTable">
                <h3>Added Experience</h3>
                <table id="expTable" class="table table-striped">
                    <thead>
                    <tr>
                        <th>Experience Title</th>
                        <th>Company Name</th>
                        <th>Duration From</th>
                        <th>Duration To</th>
                        <th>Document</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody id="expTableBody">
                    <!-- Existing rows will be dynamically added here -->
                    <?php 
                    $existingExperienceTitles = [];
                    foreach ($experienceDetails as $detail) : 
                        $existingExperienceTitles[] = $detail['experience_title'];
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($detail['experience_title']) ?></td>
                            <td><?= htmlspecialchars($detail['company_name']) ?></td>
                            <td><?= htmlspecialchars($detail['duration_from']) ?></td>
                            <td><?= htmlspecialchars($detail['duration_to']) ?></td>
                            <td><a href="<?= htmlspecialchars($detail['document_path']) ?>" target="_blank">View Document</a></td>
                            <td>
                                <button type="button" class="remove-button" 
                                        onclick="removeTableRow(this, '<?= htmlspecialchars($detail['id']) ?>', '<?= htmlspecialchars($detail['experience_title']) ?>')">-</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <input type="hidden" id="existingExperienceTitles" value="<?= htmlspecialchars(json_encode($existingExperienceTitles)) ?>">

            <div class="form-section">
                <input type="button" value="Back" onclick="location.href='eduquli.php'">
                <input type="button" value="Next" onclick="location.href='candiupload.php'">
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const existingExperienceTitles = JSON.parse(document.getElementById('existingExperienceTitles').value);
    const titleSelect = document.getElementById('experience_title_0');

    // Remove existing experience titles from the dropdown
    existingExperienceTitles.forEach(title => {
        const option = titleSelect.querySelector(`option[value="${title}"]`);
        if (option) {
            option.remove();
        }
    });

    // Handle form submission via AJAX
    document.getElementById('experienceForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    // Add new row to the table
                    const tableBody = document.getElementById('expTableBody');
                    const newRow = document.createElement('tr');

                    newRow.innerHTML = `
                        <td>${response.experience_title}</td>
                        <td>${response.company_name}</td>
                        <td>${response.duration_from}</td>
                        <td>${response.duration_to}</td>
                        <td><a href="${response.document_path}" target="_blank">View Document</a></td>
                        <td>
                            <button type="button" class="remove-button" 
                                    onclick="removeTableRow(this, '${response.id}', '${response.experience_title}')">-</button>
                        </td>
                    `;

                    tableBody.appendChild(newRow);

                    // Remove the added experience title from the dropdown
                    const option = titleSelect.querySelector(`option[value="${response.experience_title}"]`);
                    if (option) {
                        option.remove();
                    }

                    // Reset form fields
                    document.getElementById('experienceForm').reset();
                } else {
                    alert('Error adding data: ' + response.message);
                }
            } else {
                alert('Error adding data: ' + xhr.statusText);
            }
        };

        xhr.send(formData);
    });

    // Function to remove experience row
    window.removeTableRow = function(button, id, experienceTitle) {
        const rowToRemove = button.closest('tr');

        // AJAX call to delete data from database
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    // On success, remove row from table
                    rowToRemove.remove();

                    // Add the experience title back to the dropdown
                    const newOption = document.createElement('option');
                    newOption.value = experienceTitle;
                    newOption.textContent = experienceTitle;
                    titleSelect.appendChild(newOption);
                } else {
                    alert('Error deleting data: ' + response.message);
                }
            } else {
                alert('Error deleting data: ' + xhr.statusText);
            }
        };
        const formData = `id=${id}&experience_title=${experienceTitle}`;
        xhr.send(formData);
    };
});
</script>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
