<?php
session_start();

// Ensure the user is logged in and the application_no is available
if (!isset($_SESSION['application_no']) || !isset($_SESSION['login_user_id'])) {
    die("User not logged in or application number not set.");
}

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$admin_dbname = "admin";
$post_dbname = "post";

// Create connections
$connAdmin = new mysqli($servername, $username, $password, $admin_dbname);
$connPost = new mysqli($servername, $username, $password, $post_dbname);

// Check connections
if ($connAdmin->connect_error) {
    die("Admin database connection failed: " . $connAdmin->connect_error);
}
if ($connPost->connect_error) {
    die("Post database connection failed: " . $connPost->connect_error);
}

// Function to determine the highest education level based on hierarchy
function getHighestEducation($selectedEducations) {
    $hierarchy = ['postgraduation' => 4, 'graduation' => 3, '12th' => 2, '10th' => 1];
    $highestLevel = '10th'; // Default to the lowest

    foreach ($selectedEducations as $education) {
        if (isset($hierarchy[$education]) && $hierarchy[$education] > $hierarchy[$highestLevel]) {
            $highestLevel = $education;
        }
    }

    return $highestLevel;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['columns']) && is_array($_POST['columns'])) {
        $columns = $_POST['columns'];
        $application_no = $_SESSION['application_no'];
        $user_id = $_SESSION['login_user_id']; // Assuming you have a user ID in the session

        // Prepare to collect education levels
        $selectedEducations = [];

        // Prepare and execute the insert query for each selected table
        foreach ($columns as $post_name) {
            // Ensure post names are valid to prevent SQL injection
            $post_name = $connPost->real_escape_string($post_name);

            // Construct the table name based on the post name
            $table = $post_name;

            // Check if the record already exists
            $check_sql = "SELECT COUNT(*) FROM `$table` WHERE application_no = ?";
            $check_stmt = $connPost->prepare($check_sql);

            if ($check_stmt === false) {
                die("Prepare failed: " . $connPost->error);
            }

            $check_stmt->bind_param("s", $application_no);
            $check_stmt->execute();
            $check_stmt->bind_result($count);
            $check_stmt->fetch();
            $check_stmt->close();

            if ($count == 0) {
                // Construct the insert query
                $insert_sql = "INSERT INTO `$table` (application_no) VALUES (?)";
                $insert_stmt = $connPost->prepare($insert_sql);

                if ($insert_stmt === false) {
                    die("Prepare failed: " . $connPost->error);
                }

                $insert_stmt->bind_param("s", $application_no);

                if (!$insert_stmt->execute()) {
                    echo "Insert failed: " . $insert_stmt->error . "<br>";
                } else {
                    echo "Insert successful for table: $table<br>";
                }

                $insert_stmt->close();
            } else {
                echo "Record already exists in table: $table<br>";
            }

            // Fetch and collect the education level for the post
            $education_sql = "SELECT mandedu FROM post WHERE posts = ?";
            $education_stmt = $connAdmin->prepare($education_sql);

            if ($education_stmt === false) {
                die("Prepare failed: " . $connAdmin->error);
            }

            $education_stmt->bind_param("s", $post_name);
            $education_stmt->execute();
            $education_stmt->bind_result($mandedu);
            if ($education_stmt->fetch()) {
                $selectedEducations[] = $mandedu;
            }
            $education_stmt->close();
        }

        // Determine the highest education level
        $highestEducation = getHighestEducation($selectedEducations);
        $_SESSION['highest_education'] = $highestEducation;

        // Create connection for user's database
        $dbname = $_SESSION['dbbnaam']; // Your database name
        $connuser = new mysqli($servername, $username, $password, $dbname);

        if ($connuser->connect_error) {
            die("User database connection failed: " . $connuser->connect_error);
        }

        // Update the form_accessed column to 1
        $update_sql = "UPDATE register SET form_accessed = 1 WHERE user_id = ?"; // Replace 'register' with your actual table name
        $update_stmt = $connuser->prepare($update_sql);

        if ($update_stmt === false) {
            die("Prepare failed: " . $connuser->error);
        }

        $update_stmt->bind_param("s", $user_id);
        if (!$update_stmt->execute()) {
            echo "Update failed: " . $update_stmt->error . "<br>";
        }

        $update_stmt->close();

        // Redirect to persdet.php after successful update
        header("Location: persdet.php");
        exit();
    } else {
        echo "No columns selected.";
    }
} else {
    $dbbnaam = $connAdmin->real_escape_string($_SESSION['dbbnaam']);

    // Fetch posts from the broadcasts table
    $sql = "SELECT post FROM broadcasts WHERE advertisement = ?";
    $stmt = $connAdmin->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . $connAdmin->error);
    }

    $stmt->bind_param("s", $dbbnaam);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        die("Query failed: " . $connAdmin->error);
    }

    // Fetch all post names from the post table
    $sqlPosts = "SELECT DISTINCT posts, mandedu FROM post";
    $stmtPosts = $connAdmin->prepare($sqlPosts);

    if ($stmtPosts === false) {
        die("Prepare failed: " . $connAdmin->error);
    }

    $stmtPosts->execute();
    $resultPosts = $stmtPosts->get_result();

    if (!$resultPosts) {
        die("Query failed: " . $connAdmin->error);
    }

    // Fetch all table names in the post database
    $tablesResult = $connPost->query("SHOW TABLES");
    $userTables = [];
    
    $app_no = $_SESSION['application_no'];

    // Check each table for matching application_no
    while ($tableRow = $tablesResult->fetch_array()) {
        $tableName = $tableRow[0];
        $check_sql = "SELECT COUNT(*) FROM `$tableName` WHERE application_no = ?";
        $check_stmt = $connPost->prepare($check_sql);

        if ($check_stmt === false) {
            die("Prepare failed: " . $connPost->error);
        }

        $check_stmt->bind_param("s", $app_no);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count > 0) {
            $userTables[] = $tableName;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Posts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .form-container {
            width: 80%;
            margin: 10% auto 0; /* top margin of 10%, center horizontally */
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #343a40;
            margin-top: 0; /* Removed margin-top to align with form-container */
        }
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        th {
            background-color: #007bff;
            color: #ffffff;
        }
        td {
            background-color: #f8f9fa;
        }
        .checkbox-cell {
            text-align: center;
        }
        button {
            display: block;
            width: 150px;
            margin: 20px auto;
            padding: 10px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
     <?php include('navnew.php'); ?>

    <div class="form-container">
        <h1>List of Posts</h1>
        <form action="" method="post">
            <table>
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Post Name</th>
                        <th>Minimum Education</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Fetch and display posts from the 'broadcasts' table
                $broadcastPosts = [];
                while ($row = $result->fetch_assoc()) {
                    $broadcastPosts = array_merge($broadcastPosts, explode(',', $row['post']));
                }
                $broadcastPosts = array_map('trim', $broadcastPosts);
                $broadcastPosts = array_unique($broadcastPosts);

                // Display posts and match them with the posts in the 'post' table
                while ($rowPost = $resultPosts->fetch_assoc()) {
                    $post_name = htmlspecialchars($rowPost['posts']);
                    $madedu = htmlspecialchars($rowPost['mandedu']);

                    // Disable the checkbox if the post is in the userTables array
                    $disabled = in_array($post_name, $userTables) ? 'disabled' : '';

                    if (in_array($post_name, $broadcastPosts)) {
                        echo "<tr>";
                        echo "<td class='checkbox-cell'><input type='checkbox' name='columns[]' value='" . $post_name . "' $disabled></td>";
                        echo "<td>" . $post_name . "</td>";
                        echo "<td>" . $madedu . "</td>";
                        echo "</tr>";
                    }
                }

                // Free result sets
                $result->free();
                $resultPosts->free();
                ?>
                </tbody>
            </table>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>