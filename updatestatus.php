<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicationNumber = $_POST['applicationNumber'];
    $status = $_POST['status'];

    if (empty($applicationNumber) || empty($status)) {
        echo 'Invalid input.';
        exit;
    }

    $conn = connectDatabase('adv101'); // Use the appropriate database connection

    $table = $status === 'shortlisted' ? 'shortlisted' : 'rejected';
    $sql = "INSERT INTO $table (application_number) VALUES ('$applicationNumber')";

    if ($conn->query($sql) === TRUE) {
        echo 'Success';
    } else {
        echo 'Error: ' . $conn->error;
    }

    $conn->close();
}
?>
