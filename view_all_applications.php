<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Advertisement</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 20px;
        }
        .container {
            max-width: 800px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 10%;

        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #343a40;
        }
        .adv-list {
            margin-bottom: 20px;
        }
        .adv-item {
            cursor: pointer;
            padding: 15px;
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
            margin-bottom: 10px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background-color 0.3s;
        }
        .adv-item:hover {
            background-color: #ced4da;
        }
        .adv-item span {
            font-weight: bold;
            color: #495057;
        }
        .form-section input[type=button]  {
            width: auto;
            margin-left: 1px;
            margin-top: 5px;

            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .adv-item button {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .adv-item button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<?php include('navnew.php'); ?>

    <div class="container">
        <h1>Select Advertisement</h1>
        <div class="adv-list">
            <?php
            function getDatabases() {
                $host = "localhost";
                $username = 'root';
                $dbpassword = '';
                $adminDbname = 'admin';

                $adminCon = mysqli_connect($host, $username, $dbpassword, $adminDbname);

                if (!$adminCon) {
                    die("Connection to admin database failed! " . mysqli_connect_error());
                }

                $sql = "SELECT advertisement FROM broadcasts";
                $result = $adminCon->query($sql);

                $databases = [];
                while ($row = $result->fetch_assoc()) {
                    $databases[] = $row['advertisement'];
                }

                $adminCon->close();
                return $databases;
            }

            $databases = getDatabases();
            foreach ($databases as $dbname) {
                echo "<div class='adv-item'>";
                echo "<span>$dbname</span>";
                echo "<button onclick=\"window.location.href='view_applicants.php?adv=$dbname'\">View Applicants</button>";
                echo "</div>";
            }
            ?>
        </div><div class="form-section">
                <input type="button" value="Back" onclick="location.href='adminhomepage.php'">
                 </div>
    </div>
</body>
</html>
