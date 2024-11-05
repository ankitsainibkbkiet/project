<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['isNextButtonEnabled'])) {
        $_SESSION['isNextButtonEnabled'] = filter_var($_POST['isNextButtonEnabled'], FILTER_VALIDATE_BOOLEAN);
    }
}
?>
