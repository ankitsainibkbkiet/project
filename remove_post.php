<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_name = $_POST['post_name'];

    // Connect to MySQL
    $servername = "localhost";
    $username = "root";
    $password = "";  // Update with your MySQL password
    $post_db = "post";  // Post database
    $admin_db = "admin";  // Admin database

    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Remove the post from the post database
    $conn->select_db($post_db);
    $sql = "DROP TABLE $post_name";
    if ($conn->query($sql) === TRUE) {
        // Remove the post from the admin database's post table
        $conn->select_db($admin_db);
        $sql = "DELETE FROM post WHERE posts LIKE '%$post_name%'";
        if ($conn->query($sql) === TRUE) {
            // Update the broadcasts table in the admin database
            $sql = "UPDATE broadcasts
                    SET post = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', post, ','), ',$post_name,', ','))
                    WHERE post LIKE '%$post_name%'";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => "Error updating broadcasts table: " . $conn->error]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => "Error deleting from admin database: " . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => "Error deleting post table: " . $conn->error]);
    }

    $conn->close();
}
?>
 