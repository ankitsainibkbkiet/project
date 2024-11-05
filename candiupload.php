<?php
session_start();

// Function to insert or update user document data in the database
function insertUserDocuments($id, $aadharPath, $photoPath, $signaturePath) {
    $host = "localhost";
    $username = "root";
    $dbpassword = ""; // Fill in your database password
    $dbname = $_SESSION['dbbnaam']; // Your database name

    $con = mysqli_connect($host, $username, $dbpassword, $dbname);

    if (!$con) {
        die(json_encode(["status" => "error", "message" => "Connection failed! " . mysqli_connect_error()]));
    }

    // Check if record exists for the user ID
    $stmt = $con->prepare("SELECT * FROM user_documents WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the existing record
        $stmt = $con->prepare("UPDATE user_documents SET 
            aadhar_path = COALESCE(?, aadhar_path), photo_path = COALESCE(?, photo_path), signature_path = COALESCE(?, signature_path) 
            WHERE id = ?");
        $stmt->bind_param("ssss", $aadharPath, $photoPath, $signaturePath, $id);
    } else {
        // Insert a new record
        $stmt = $con->prepare("INSERT INTO user_documents (
            id, aadhar_path, photo_path, signature_path 
        ) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $id, $aadharPath, $photoPath, $signaturePath);
    }

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
    mysqli_close($con);
}

// Function to fetch user document details
function fetchUserDocuments($userId) {
    $host = "localhost";
    $username = "root";
    $dbpassword = ""; // Fill in your database password
    $dbname = $_SESSION['dbbnaam']; // Your database name

    $con = mysqli_connect($host, $username, $dbpassword, $dbname);

    if (!$con) {
        return [];
    }

    $stmt = $con->prepare("SELECT * FROM user_documents WHERE id = ?");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    if ($row = $result->fetch_assoc()) {
        $data = $row;
    }

    $stmt->close();
    mysqli_close($con);

    return $data;
}

// Function to remove a specific document type
function removeDocument($userId, $documentType) {
    $host = "localhost";
    $username = "root";
    $dbpassword = ""; // Fill in your database password
    $dbname = $_SESSION['dbbnaam']; // Your database name

    $con = mysqli_connect($host, $username, $dbpassword, $dbname);

    if (!$con) {
        die(json_encode(["status" => "error", "message" => "Connection failed! " . mysqli_connect_error()]));
    }

    // Update the specific document path to null
    $stmt = $con->prepare("UPDATE user_documents SET $documentType = NULL WHERE id = ?");
    $stmt->bind_param("s", $userId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
    mysqli_close($con);
}

// Process POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_SESSION['login_user_id'];

    // Handle document removal
    if (isset($_POST['remove'])) {
        $documentType = $_POST['remove'];
        removeDocument($id, $documentType);
        exit;
    }

    $aadharPath = null;
    $photoPath = null;
    $signaturePath = null;

    // Handle file upload for Aadhar
    if (isset($_FILES['aadhar_document']) && $_FILES['aadhar_document']['error'] == 0) {
        $targetDir = "uploads/";
        $aadharPath = $targetDir . basename($_FILES["aadhar_document"]["name"]);

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (!move_uploaded_file($_FILES["aadhar_document"]["tmp_name"], $aadharPath)) {
            die(json_encode(["status" => "error", "message" => "Failed to upload Aadhar document."]));
        }
    }

    // Handle file upload for Photo
    if (isset($_FILES['photo_document']) && $_FILES['photo_document']['error'] == 0) {
        $targetDir = "uploads/";
        $photoPath = $targetDir . basename($_FILES["photo_document"]["name"]);

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (!move_uploaded_file($_FILES["photo_document"]["tmp_name"], $photoPath)) {
            die(json_encode(["status" => "error", "message" => "Failed to upload Photo document."]));
        }
    }

    // Handle file upload for Signature
    if (isset($_FILES['signature_document']) && $_FILES['signature_document']['error'] == 0) {
        $targetDir = "uploads/";
        $signaturePath = $targetDir . basename($_FILES["signature_document"]["name"]);

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (!move_uploaded_file($_FILES["signature_document"]["tmp_name"], $signaturePath)) {
            die(json_encode(["status" => "error", "message" => "Failed to upload Signature document."]));
        }
    }

    insertUserDocuments($id, $aadharPath, $photoPath, $signaturePath);
    exit;
}

$userId = $_SESSION['login_user_id'];
$userDocuments = fetchUserDocuments($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Upload Form</title>
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
            padding: 20px;
            flex: 1;
        }

        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            margin-top: 150px;
            margin-left: 220px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
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
            margin-left: 10px;
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

        .form-section a {
            margin-left: 10px;
            color: #0d416b;
            text-decoration: none;
        }

        .form-section a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<?php include('navbar2.php'); ?>
<?php include('navnew.php'); ?>

<div class="content">
    <div class="form-container">
        <h2>Upload Documents</h2>
        <form id="uploadForm" method="POST" enctype="multipart/form-data">
            <div class="form-section">
                <label for="aadhar_document">Aadhar Document</label>
                <input type="file" id="aadhar_document" name="aadhar_document" accept=".pdf, .doc, .docx, .png, .jpg, .jpeg">
                <input type="button" value="Upload" onclick="uploadDocument('aadhar_document')">
                <?php if (!empty($userDocuments['aadhar_path'])): ?>
                    <a id="aadhar_link" href="<?= $userDocuments['aadhar_path'] ?>" target="_blank">View Already Uploaded Document</a>
                    <input type="button" value="Remove" onclick="removeDocument('aadhar_path')">
                <?php endif; ?>
            </div>

            <div class="form-section">
                <label for="photo_document">Photo Document</label>
                <input type="file" id="photo_document" name="photo_document" accept=".pdf, .doc, .docx, .png, .jpg, .jpeg">
                <input type="button" value="Upload" onclick="uploadDocument('photo_document')">
                <?php if (!empty($userDocuments['photo_path'])): ?>
                    <a id="photo_link" href="<?= $userDocuments['photo_path'] ?>" target="_blank">View Already Uploaded Document</a>
                    <input type="button" value="Remove" onclick="removeDocument('photo_path')">
                <?php endif; ?>
            </div>

            <div class="form-section">
                <label for="signature_document">Signature Document</label>
                <input type="file" id="signature_document" name="signature_document" accept=".pdf, .doc, .docx, .png, .jpg, .jpeg">
                <input type="button" value="Upload" onclick="uploadDocument('signature_document')">
                <?php if (!empty($userDocuments['signature_path'])): ?>
                    <a id="signature_link" href="<?= $userDocuments['signature_path'] ?>" target="_blank">View Already Uploaded Document</a>
                    <input type="button" value="Remove" onclick="removeDocument('signature_path')">
                <?php endif; ?>
            </div>

            <div class="form-section">
                <input type="button" id="nextButton" value="Next" onclick="checkAndRedirect()">
            </div>
        </form>
    </div>
</div>

<script>
    function updateNextButtonState() {
        const aadharUploaded = document.getElementById('aadhar_link') !== null;
        const photoUploaded = document.getElementById('photo_link') !== null;
        const signatureUploaded = document.getElementById('signature_link') !== null;
        const nextButton = document.getElementById('nextButton');
        
        nextButton.disabled = !(aadharUploaded && photoUploaded && signatureUploaded);
    }

    function checkAndRedirect() {
        const nextButton = document.getElementById('nextButton');
        
        if (!nextButton.disabled) {
            location.href = 'printable.php';
        } else {
            alert('Please upload all required documents before proceeding.');
        }
    }

    function uploadDocument(documentId) {
        const inputElement = document.getElementById(documentId);
        const file = inputElement.files[0];
        const formData = new FormData();
        formData.append(documentId, file);

        fetch('', {
            method: 'POST',
            body: formData
        }).then(response => response.json()).then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }

    function removeDocument(documentType) {
        const formData = new FormData();
        formData.append('remove', documentType);

        fetch('', {
            method: 'POST',
            body: formData
        }).then(response => response.json()).then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }

    // Initialize the state of the Next button on page load
    document.addEventListener('DOMContentLoaded', updateNextButtonState);

    // Add event listeners to file inputs to update the Next button state when files are selected
    document.getElementById('aadhar_document').addEventListener('change', updateNextButtonState);
    document.getElementById('photo_document').addEventListener('change', updateNextButtonState);
    document.getElementById('signature_document').addEventListener('change', updateNextButtonState);
</script>
</body>
</html>
