<?php
session_start();

if (isset($_SESSION['login_user_id'])) {
    $id = $_SESSION['login_user_id'];
    $host = "localhost";
    $username = "root";
    $dbpassword = ""; // Fill in your database password
    $dbname = $_SESSION['dbbnaam']; // Your database name

    $con = mysqli_connect($host, $username, $dbpassword, $dbname);
    
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $stmt = $con->prepare("SELECT disability_certificate FROM personal_details WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $filePath = $row['disability_certificate'];

        if (file_exists($filePath)) {
            // Determine content type based on file extension
            $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $contentType = '';

            switch ($fileExtension) {
                case 'pdf':
                    $contentType = 'application/pdf';
                    break;
                case 'jpg':
                case 'jpeg':
                    $contentType = 'image/jpeg';
                    break;
                case 'png':
                    $contentType = 'image/png';
                    break;
                case 'gif':
                    $contentType = 'image/gif';
                    break;
                case 'txt':
                    $contentType = 'text/plain';
                    break;
                case 'csv':
                    $contentType = 'text/csv';
                    break;
                default:
                    $contentType = 'application/octet-stream'; // default to binary if unknown
                    break;
            }

            header('Content-Type: ' . $contentType);
            header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
            readfile($filePath);
        } else {
            echo "File does not exist.";
        }
    } else {
        echo "No file found.";
    }

    $stmt->close();
    mysqli_close($con);
} else {
    echo "User not logged in.";
}
?>
