<?php
session_start();

// Function to upload a file and return its path
function uploadFile($file, $uploadDir) {
    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $targetFile = $uploadDir . basename($file["name"]);

    if ($file["error"] != UPLOAD_ERR_OK) {
        return null;
    }

    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return $targetFile;
    } else {
        return null;
    }
}

// Function to update the file path in the database and return the new file path
function updateFilePath($userId, $filePath) {
    $host = 'localhost';
    $dbname = $_SESSION['dbbnaam']; // Your database name
    $username = 'root';
    $password = '';

    // Connect to the database
    $con = mysqli_connect($host, $username, $password, $dbname);

    if (!$con) {
        die(json_encode(["status" => "error", "message" => "Connection failed! " . mysqli_connect_error()]));
    }

    // Check if a file path already exists
    $stmt = $con->prepare("SELECT category_certificate FROM personal_details WHERE id = ?");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $oldFilePath = $row ? $row['category_certificate'] : null;

    // Update the file path in the database
    $stmt = $con->prepare("UPDATE personal_details SET category_certificate = ? WHERE id = ?");
    $stmt->bind_param("ss", $filePath, $userId);

    if ($stmt->execute()) {
        // Optionally delete the old file
        if ($oldFilePath && file_exists($oldFilePath)) {
            unlink($oldFilePath);
        }
        $stmt->close();
        mysqli_close($con);
        return $filePath; // Return the new file path
    } else {
        $stmt->close();
        mysqli_close($con);
        return null; // Return null in case of error
    }
}

// Handle file upload and database update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file']) && isset($_POST['upload_type']) && isset($_SESSION['login_user_id'])) {
    $uploadDir = 'uploads/';
    $file = $_FILES['file'];
    $uploadType = $_POST['upload_type'];
    $userId = $_SESSION['login_user_id'];

    // Upload the file
    $uploadedFilePath = uploadFile($file, $uploadDir);

    if ($uploadedFilePath) {
        $_SESSION[$uploadType] = $uploadedFilePath;
        $assd = updateFilePath($userId, $uploadedFilePath); // Store the most recent file path
        if ($assd) {
            echo json_encode(["status" => "success", "message" => "File path updated successfully.", "file_path" => $assd]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database update failed."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "File upload failed."]);
    }
}
?>
