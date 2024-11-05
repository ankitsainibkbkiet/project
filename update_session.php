<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['isNextButtonEnabled'])) {
    $_SESSION['isnextenabled'] = $_POST['isNextButtonEnabled'] === 'true' ? true : false;
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
