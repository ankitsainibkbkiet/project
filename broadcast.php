<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Advertisement</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2014/06/16/23/40/grey-370125_640.png');
            background-size: cover;
            background-position: center;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 800px;
            margin-top: 40%;

        }

        .form-container h1 {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group input[type="file"],
        .form-group select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .form-group button,
        .form-group input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .form-group button:hover,
        .form-group input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .form-group input[type="radio"] {
            margin-right: 10px;
        }
    </style>
    <script>
        function togglePostOption() {
            const existingPostOption = document.getElementById('existing_post').checked;
            document.getElementById('select_posts').style.display = existingPostOption ? 'block' : 'none';
            document.getElementById('new_post_field').style.display = existingPostOption ? 'none' : 'block';
        }

        function addNewPost() {
            const newPostName = document.getElementById('new_post_name').value;
            const educations = Array.from(document.querySelectorAll('input[name="new_educations[]"]:checked')).map(checkbox => checkbox.value);
            const customEducation = document.getElementById('custom_education').value;

            if (newPostName.trim() === '') {
                alert('Please enter a post name');
                return;
            }

            if (educations.length === 0 && customEducation.trim() === '') {
                alert('Please select at least one education option or enter custom education');
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'add_new_post.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert('Post added successfully');
                        document.getElementById('existing_post').checked = true;
                        togglePostOption();
                        location.reload();  // Reload the page to update the post list
                    } else {
                        alert('Error: ' + response.error);
                    }
                }
            };
            xhr.send('new_post_name=' + encodeURIComponent(newPostName) + 
                     '&new_educations=' + encodeURIComponent(educations.join(',')) + 
                     '&custom_education=' + encodeURIComponent(customEducation));
        }
    </script><script>
    function removePost(postName) {
        if (confirm('Are you sure you want to remove this post?')) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'remove_post.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert('Post removed successfully');
                        location.reload();  // Reload the page to update the post list
                    } else {
                        alert('Error: ' + response.error);
                    }
                }
            };
            xhr.send('post_name=' + encodeURIComponent(postName));
        }
    }
</script>

</head>
<body>
    <?php include('navnew.php'); ?>

    <div class="container">
        <div class="form-container">
            <h1>Create New Advertisement</h1>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="adv_name">Create Advertisement Name:</label>
                    <input type="text" id="adv_name" name="adv_name" required>
                </div>

                <div class="form-group">
                    <label for="adv_title">Advertisement Title:</label>
                    <input type="text" id="adv_title" name="adv_title" required>
                </div>
                
                <div class="form-group">
                    <label for="adv_pdf">Upload Advertisement PDF:</label>
                    <input type="file" id="adv_pdf" name="adv_pdf" accept="application/pdf" required>
                </div>
                
                <div class="form-group">
                    <label for="date_from">From Date:</label>
                    <input type="date" id="date_from" name="date_from" required>
                </div>

                <div class="form-group">
                    <label for="date_to">To Date:</label>
                    <input type="date" id="date_to" name="date_to" required>
                </div>

                <div class="form-group">
                    <label>Choose Post Option:</label><br>
                    <input type="radio" id="existing_post" name="post_option" value="existing" onclick="togglePostOption()" checked>
                    <label for="existing_post">Choose Existing Posts</label><br>
                    <input type="radio" id="new_post" name="post_option" value="new" onclick="togglePostOption()">
                    <label for="new_post">Create New Post</label>
                </div>

                <div id="select_posts" class="form-group">
    <label>Select Posts:</label><br>
    <?php
    // Connect to MySQL
    $servername = "localhost";
    $username = "root";
    $password = "";  // Update with your MySQL password
    $post_db = "post";  // Change this to your post database name

    $conn = new mysqli($servername, $username, $password, $post_db);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch table names from the post database
    $result = $conn->query("SHOW TABLES");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array()) {
            $table_name = $row[0];
            echo '<div class="form-check">';
            echo '<input type="checkbox" class="form-check-input" name="posts[]" value="' . $table_name . '" id="' . $table_name . '">';
            echo '<label class="form-check-label" for="' . $table_name . '">' . $table_name . '</label>';
            echo '<button type="button" class="btn btn-danger btn-sm" onclick="removePost(\'' . $table_name . '\')">Remove</button>';
            echo '</div>';
        }
    } else {
        echo "No tables found in the post database.<br>";
    }

    $conn->close();
    ?>
</div>


                <div id="new_post_field" class="form-group" style="display: none;">
                    <label for="new_post_name">New Post Name:</label>
                    <input type="text" id="new_post_name" name="new_post_name">
                    <br><br>
                    <label>Select Mandatory Educations for New Post:</label><br>
                    <?php
                    $education_levels = ['10th', '12th', 'Graduation', 'Post-Graduation'];
                    foreach ($education_levels as $level) {
                        echo '<div class="form-check">';
                        echo '<input type="radio" class="form-check-input" name="new_educations[]" value="' . strtolower($level) . '" id="' . strtolower($level) . '">';
                        echo '<label class="form-check-label" for="' . strtolower($level) . '">' . $level . '</label>';
                        echo '</div>';
                    }
                    ?>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="new_educations[]" value="custom" id="custom_education_radio">
                        <label class="form-check-label" for="custom_education_radio">Custom:</label>
                        <input type="text" id="custom_education" name="custom_education" class="form-control">
                    </div>
                    <br>
                    <button type="button" class="btn btn-primary" onclick="addNewPost()">Add</button>
                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Submit">
                </div>
            </form>
            <div class="form-section">
                <input type="button" value="Back" onclick="location.href='adminhomepage.php'">
                 </div>
        </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>


    <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adv_name = $_POST['adv_name'];
    $adv_title = $_POST['adv_title'];
    $post_option = $_POST['post_option'];
    $educations = isset($_POST['new_educations']) ? $_POST['new_educations'] : [];
    $selected_posts = isset($_POST['posts']) ? $_POST['posts'] : [];
    $custom_education = isset($_POST['custom_education']) ? $_POST['custom_education'] : '';
    
    // New date fields
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];

    // Handle file upload
    $upload_dir = 'uploads/';
    $uploaded_file = $upload_dir . basename($_FILES['adv_pdf']['name']);
    $upload_success = move_uploaded_file($_FILES['adv_pdf']['tmp_name'], $uploaded_file);

    if (!$upload_success) {
        die("Error uploading file.");
    }

    // Connect to MySQL
    $servername = "localhost";
    $username = "root";
    $password = "";  // Update with your MySQL password

    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create new database for the advertisement
    $db_name = $adv_name;
    $sql = "CREATE DATABASE $db_name";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully<br>";
    } else {
        echo "Error creating database: " . $conn->error;
    }

    // Select the new database
    $conn->select_db($db_name);

    // SQL to create tables
    $tables = [
        "register" => "CREATE TABLE IF NOT EXISTS register (
            User_id INT AUTO_INCREMENT PRIMARY KEY,
            adv_no VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            phone VARCHAR(20),form_accessed TINYINT(1) DEFAULT 0,
            name VARCHAR(255),
            password VARCHAR(255),
            registration_date DATE,
            is_finalized TINYINT DEFAULT 0,shortlisted int default 0,
            application_no VARCHAR(255)
        )",
        "personal_details" => "CREATE TABLE IF NOT EXISTS personal_details (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(255),
            dob DATE,
            registered_email VARCHAR(255),
            registered_mobile VARCHAR(20),
            aadhaar_no VARCHAR(20),
            gender VARCHAR(10),
            marital_status VARCHAR(20),
            nationality VARCHAR(50),
            state VARCHAR(50),
            mothers_name VARCHAR(255),
            fathers_name VARCHAR(255),
            alternate_mobile VARCHAR(20),
            category VARCHAR(50),
            ex_serviceman TINYINT,
            specify_ex_serviceman TEXT,
            differently_abled TINYINT,
            disability_percentage DECIMAL(5,2),
            disability_type VARCHAR(100),
            corrospondance_address TEXT, 
            corrospondance_city VARCHAR(100),
            permanent_city VARCHAR(100),
            permanent_address TEXT,
            disability_certificate VARCHAR(255),
            category_certificate VARCHAR(255),
            service_no VARCHAR(50),
            date_of_retirement DATE
        )",
        "education_details" => "CREATE TABLE IF NOT EXISTS education_details (
            id INT ,
            education_type VARCHAR(50),
            document_path VARCHAR(255),
            board VARCHAR(50),
            percentage DECIMAL(5,2),
            school_name VARCHAR(255)
        )",
        "experience_details" => "CREATE TABLE IF NOT EXISTS experience_details (
            id INT ,
            experience_title VARCHAR(255),
            company_name VARCHAR(255),
            duration_from DATE,
            duration_to DATE,
            document_path VARCHAR(255)
        )",
        "user_documents" => "CREATE TABLE IF NOT EXISTS user_documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            photo_path VARCHAR(255),
            aadhar_path VARCHAR(255),
            signature_path VARCHAR(255)
        )"
    ];

    foreach ($tables as $table_name => $table_sql) {
        if ($conn->query($table_sql) === TRUE) {
            echo "Table '$table_name' created successfully<br>";
        } else {
            echo "Error creating table '$table_name': " . $conn->error;
        }
    }

    // Connect to admin database to update broadcasts table
    $admin_db = "admin";  // Change this to your admin database name
    $conn->select_db($admin_db);

    // Insert into broadcasts table with date fields
    $posts_str = implode(", ", $selected_posts);
    $educations_str = implode(", ", $educations);
    $sql = "INSERT INTO broadcasts (advertisement, adv_title, post, path, date_from, date_to) 
            VALUES ('$adv_name', '$adv_title', '$posts_str', '$uploaded_file', '$date_from', '$date_to')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully in broadcasts table<br>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close connection
    $conn->close();
}
?>

 