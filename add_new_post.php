<?php
$servername = "localhost";
$username = "root";
$password = "";  // Update with your MySQL password
$post_db = "post";  // Change this to your post database name
$admin_db = "admin";  // Change this to your admin database name

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_post_name = $_POST['new_post_name'];
    $new_educations = isset($_POST['new_educations']) ? explode(',', $_POST['new_educations']) : [];

    // Connect to MySQL
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create new table in the post database
    $conn->select_db($post_db);
    $sql = "CREATE TABLE $new_post_name (
        application_no VARCHAR(255) PRIMARY KEY
    )";

    if ($conn->query($sql) === TRUE) {
        // Insert into post table in the admin database
        $conn->select_db($admin_db);
        $educations_str = implode(", ", $new_educations);
        $sql = "INSERT INTO post (posts, mandedu) VALUES ('$new_post_name', '$educations_str')";
        if ($conn->query($sql) === TRUE) {
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'error' => $conn->error];
        }
    } else {
        $response = ['success' => false, 'error' => $conn->error];
    }

    // Return response as JSON
    echo json_encode($response);

    $conn->close();
}
?>
