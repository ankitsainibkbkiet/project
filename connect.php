<?php

function getDbCredentials($adv_no) {
    // The advertisement number is the dbname
    return [
        'dbname' => $adv_no,
        'username' => 'root',
        'password' => ''
    ];
}

function connectDatabase($adv_no) {
    $credentials = getDbCredentials($adv_no);
    $host = "localhost";
    $username = $credentials['username'];
    $dbpassword = $credentials['password'];
    $dbname = $credentials['dbname'];

    $con = mysqli_connect($host, $username, $dbpassword, $dbname);

    if (!$con) {
        die("Connection failed! " . mysqli_connect_error());
    }

    return $con;
}

function getDbNamesFromAdmin() {
    $adminHost = "localhost";
    $adminUsername = "root";
    $adminPassword = "";
    $adminDbname = "admin";

    $adminCon = mysqli_connect($adminHost, $adminUsername, $adminPassword, $adminDbname);

    if (!$adminCon) {
        die("Connection to admin database failed! " . mysqli_connect_error());
    }

    $query = "SELECT advertisement FROM broadcasts";
    $result = mysqli_query($adminCon, $query);

    $credentials = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $credentials[$row['advertisement']] = [
            'dbname' => $row['advertisement'],
            'username' => 'root',
            'password' => ''
        ];
    }

    mysqli_close($adminCon);
    return $credentials;
}

?>
