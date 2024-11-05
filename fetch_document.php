<?php
include('connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $advertisement = $_POST['advertisement'];

    // Database configuration
    $servername = "localhost"; // Replace with your database server name
    $username = "root";        // Replace with your database username
    $password = "";            // Replace with your database password
    $dbname = "admin";         // The name of the database to connect to

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT path FROM broadcasts WHERE advertisement=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $advertisement);
    $stmt->execute();
    $stmt->bind_result($path);
    $stmt->fetch();

    if ($path) {
        $response = ['success' => true, 'path' => $path];
    } else {
        $response = ['success' => false];
    }

    echo json_encode($response);

    $stmt->close();
    $conn->close();
}
?>
