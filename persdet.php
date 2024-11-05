<?php
session_start();

function insertOrUpdateUser($id, $full_name, $dob, $registered_email, $registered_mobile, $aadhaar_no, 
$gender,  $marital_status, $nationality, $state, 
$mothers_name, $fathers_name, $alternate_mobile,  
$category, $ex_serviceman, $specify_ex_serviceman, $date_of_retirement, $service_no, 
$differently_abled, $disability_percentage, $disability_type, 
$corrospondance_address, $permanent_address, $corrospondance_city, 
$permanent_city ) {

    $host = "localhost";
    $username = "root";
    $dbpassword = ""; // Fill in your database password
     $dbname = $_SESSION['dbbnaam']; // Your database name

    $con = mysqli_connect($host, $username, $dbpassword, $dbname);

    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Use prepared statements to prevent SQL injection
    $stmt = $con->prepare("SELECT * FROM personal_details WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the existing record
        $stmt = $con->prepare("UPDATE personal_details SET 
            full_name=?, dob=?, registered_email=?, registered_mobile=?, aadhaar_no=?, 
            gender=?,   marital_status=?, nationality=?, state=?, 
            mothers_name=?, fathers_name=?, alternate_mobile=?, 
            category=?, ex_serviceman=?, specify_ex_serviceman=?, 
            date_of_retirement=?, service_no=?, differently_abled=?, 
            disability_percentage=?, disability_type=?, 
            corrospondance_address=?, permanent_address=?, corrospondance_city=?, 
            permanent_city=? WHERE id=?");

        $stmt->bind_param("sssssssssssssssssssssssss", 
            $full_name, $dob, $registered_email, $registered_mobile, $aadhaar_no, 
            $gender,   $marital_status, $nationality, $state, 
            $mothers_name, $fathers_name, $alternate_mobile,  
            $category, $ex_serviceman, $specify_ex_serviceman, $date_of_retirement, $service_no, 
            $differently_abled, $disability_percentage, $disability_type, 
            $corrospondance_address, $permanent_address, $corrospondance_city, 
            $permanent_city, $id);

    } else {
        // Insert a new record
        $stmt = $con->prepare("INSERT INTO personal_details (
            id, full_name, dob, registered_email, registered_mobile, aadhaar_no, gender, 
            marital_status, nationality, state, mothers_name, 
            fathers_name, alternate_mobile, category, 
            ex_serviceman, specify_ex_serviceman, date_of_retirement, service_no,
            differently_abled, disability_percentage, disability_type, corrospondance_address, 
            permanent_address, corrospondance_city, permanent_city
        ) VALUES (?,   ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,  ?, ?, ?, ?)");

        $stmt->bind_param("sssssssssssssssssssssssss", 
            $id, $full_name, $dob, $registered_email, $registered_mobile, $aadhaar_no, 
            $gender, $marital_status, $nationality, $state, 
            $mothers_name, $fathers_name, $alternate_mobile, 
            $category, $ex_serviceman, $specify_ex_serviceman, $date_of_retirement, $service_no,
            $differently_abled, $disability_percentage, $disability_type, 
            $corrospondance_address, $permanent_address, $corrospondance_city, 
            $permanent_city);
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = "Entries added!";
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    mysqli_close($con);
}

function fetchUserDetails($id) {
    $host = "localhost";
    $username = "root";
    $dbpassword = ""; // Fill in your database password
    $dbname = $_SESSION['dbbnaam'] ; // Your database name

    $con = mysqli_connect($host, $username, $dbpassword, $dbname);

    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $stmt = $con->prepare("SELECT * FROM personal_details WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return [];
    }

    $stmt->close();
    mysqli_close($con);
}

// Fetch user details from database and populate form fields if user is logged in
if(isset($_SESSION['login_user_id'])) {
    $id = $_SESSION['login_user_id'];
    $userDetails = fetchUserDetails($id);

    $full_name = $userDetails['full_name'] ?? 'NA';
    $dob = $userDetails['dob'] ?? 'NA';
    $registered_email = $userDetails['registered_email'] ?? 'NA';
    $registered_mobile = $userDetails['registered_mobile'] ?? 'NA';
    $aadhaar_no = $userDetails['aadhaar_no'] ?? 'NA';
    $gender = $userDetails['gender'] ?? 'NA';
     $marital_status = $userDetails['marital_status'] ?? 'NA';
    $nationality = $userDetails['nationality'] ?? 'NA';
    $state = $userDetails['state'] ?? 'NA';
    $mothers_name = $userDetails['mothers_name'] ?? 'NA';
    $fathers_name = $userDetails['fathers_name'] ?? 'NA';
    $alternate_mobile = $userDetails['alternate_mobile'] ?? 'NA';
    $category = $userDetails['category'] ?? 'NA';
    $ex_serviceman = $userDetails['ex_serviceman'] ?? 'NA';
    $specify_ex_serviceman = $userDetails['specify_ex_serviceman'] ?? 'NA';
    $date_of_retirement = $userDetails['date_of_retirement'] ?? 'NA';
    $service_no = $userDetails['service_no'] ?? 'NA';
       $corrospondance_address = $userDetails['corrospondance_address'] ?? 'NA';
    $permanent_address = $userDetails['permanent_address'] ?? 'NA';
    $corrospondance_city = $userDetails['corrospondance_city'] ?? 'NA';
    $permanent_city = $userDetails['permanent_city'] ?? 'NA';
    $differently_abled = $userDetails['differently_abled'] ?? 'NA';
    $disability_percentage = $userDetails['disability_percentage'] ?? 'NA';
    $disability_type = $userDetails['disability_type'] ?? 'NA';
    $disability_certificate = $userDetails['disability_certificate'] ?? 'NA';
}
 
if (isset($_POST['submit'])) {
    $id = $_SESSION['login_user_id'];
    $full_name = !empty($_POST['full_name']) ? $_POST['full_name'] : 'NA';
    $dob = !empty($_POST['dob']) ? $_POST['dob'] : 'NA';
    $registered_email = !empty($_POST['registered_email']) ? $_POST['registered_email'] : 'NA';
    $registered_mobile = !empty($_POST['registered_mobile']) ? $_POST['registered_mobile'] : 'NA';
    $aadhaar_no = !empty($_POST['aadhaar_no']) ? $_POST['aadhaar_no'] : 'NA';
    $gender = !empty($_POST['gender']) ? $_POST['gender'] : 'NA';
    $marital_status = !empty($_POST['marital_status']) ? $_POST['marital_status'] : 'NA';
    $nationality = !empty($_POST['nationality']) ? $_POST['nationality'] : 'NA';
    $state = !empty($_POST['state']) ? $_POST['state'] : 'NA';
    $mothers_name = !empty($_POST['mothers_name']) ? $_POST['mothers_name'] : 'NA';
    $fathers_name = !empty($_POST['fathers_name']) ? $_POST['fathers_name'] : 'NA';
    $alternate_mobile = !empty($_POST['alternate_mobile']) ? $_POST['alternate_mobile'] : 'NA';
    $category = !empty($_POST['category']) ? $_POST['category'] : 'NA';
    $ex_serviceman = !empty($_POST['ex_serviceman']) ? $_POST['ex_serviceman'] : 'NA';
    $specify_ex_serviceman = !empty($_POST['specify_ex_serviceman']) ? $_POST['specify_ex_serviceman'] : 'NA';
    $date_of_retirement = !empty($_POST['date_of_retirement']) ? $_POST['date_of_retirement'] : 'NA';
    $service_no = !empty($_POST['service_no']) ? $_POST['service_no'] : 'NA';
    $differently_abled = !empty($_POST['differently_abled']) ? $_POST['differently_abled'] : 'NA';
    $corrospondance_address = !empty($_POST['corrospondance_address']) ? $_POST['corrospondance_address'] : 'NA';
    $permanent_address = !empty($_POST['permanent_address']) ? $_POST['permanent_address'] : 'NA';
    $corrospondance_city = !empty($_POST['corrospondance_city']) ? $_POST['corrospondance_city'] : 'NA';
    $permanent_city = !empty($_POST['permanent_city']) ? $_POST['permanent_city'] : 'NA';
    
    $disability_percentage = 'NA';
    $disability_type = 'NA';
    
    if ($differently_abled === 'yes') {
        $disability_percentage = !empty($_POST['disability_percentage']) ? $_POST['disability_percentage'] : 'NA';
        $disability_type = !empty($_POST['disability_type']) ? $_POST['disability_type'] : 'NA';

    }

    insertOrUpdateUser(
        $id, $full_name, $dob, $registered_email, $registered_mobile, $aadhaar_no,
        $gender, $marital_status, $nationality, $state,
        $mothers_name, $fathers_name, $alternate_mobile,
        $category, $ex_serviceman, $specify_ex_serviceman, $date_of_retirement, $service_no,
        $differently_abled, $disability_percentage, $disability_type,
        $corrospondance_address, $permanent_address, $corrospondance_city,
        $permanent_city
    );
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CEERI PILANI Website</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            overflow-y: hidden;
        }

         

         

         

        
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 1500px;
            max-width: 1500px;
            height: 92%;
            overflow-y: auto;
            margin-left: 270px;
            margin-top: 6%;
            
        }

        .form-section {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .form-section label {
            display: inline-block;
            width: 200px;
            text-align: right;
            margin-right: 20px;
        }

        .form-section input[type=text], 
        .form-section input[type=email], 
        .form-section input[type=tel], 
        .form-section input[type=date], 
        .form-section select {
            width: calc(100% - 220px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .form-section input[type=button], 
        .form-section input[type=submit], 
        .form-section input[type=button].save-button {
            width: auto;
            margin-left: 210px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-section input[type=button].save-button {
            background-color: #008CBA; 
        }

        .form-section input[type=button]:hover,
        .form-section input[type=submit]:hover,
        .form-section input[type=button].save-button:hover {
            background-color: #45a049;
        }

        #category_section, #disability_section {
            display: none;
        }
    </style>

<script>

function viewCategory() {
    window.open('view_category.php', '_blank');
}

function viewdisability() {
    window.open('view_disability.php', '_blank');
}


function uploadFiledis(fileInputId, uploadType) {
            var formData = new FormData();
            var fileInput = document.getElementById(fileInputId);
            var file = fileInput.files[0];
            formData.append('file', file);
            formData.append('upload_type', uploadType);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload_handler_dis.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    alert('File uploaded successfully.');
                } else {
                    alert('File upload failed.');
                }
            };
            xhr.send(formData);
        }



        function goBack() {
            window.location.href = 'postselection.php';
        }
        

        function uploadFile(fileInputId, uploadType) {
            var formData = new FormData();
            var fileInput = document.getElementById(fileInputId);
            var file = fileInput.files[0];
            formData.append('file', file);
            formData.append('upload_type', uploadType);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload_handler.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    alert('File uploaded successfully.');
                } else {
                    alert('File upload failed.');
                }
            };
            xhr.send(formData);
        }
    </script>
</head>
<body>
<?php include('navbar2.php'); ?>
<?php include('navnew.php'); ?>



 
    
    <div class="form-container">
        <h2>Personal Details Form</h2>
        <form id="personal_details_form" action=" " method="post">
            <div class="form-section">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo isset($full_name) ? $full_name : ''; ?>" required><br><br>

             </div>
            <div class="form-section">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" value="<?php echo isset($dob) ? $dob : ''; ?>" required><br><br>
                </div>
            <div class="form-section">
                <label for="registered_email">Registered Email ID:</label>
                <input type="email" id="registered_email" name="registered_email" value="<?php echo isset($registered_email) ? $registered_email : ''; ?>" required><br><br>
                </div>
            <div class="form-section">
                <label for="registered_mobile">Registered Mobile No:</label>
                <input type="text" id="registered_mobile" name="registered_mobile" value="<?php echo isset($registered_mobile) ? $registered_mobile : ''; ?>" required><br><br>
                </div>
            <div class="form-section">
                <label for="aadhaar_no">Aadhaar No:</label>
                <input type="text" id="aadhaar_no" name="aadhaar_no" value="<?php echo isset($aadhaar_no) ? $aadhaar_no : ''; ?>"><br><br>
                </div>
            <div class="form-section">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" onchange="toggleDisability()">
                <option value="male" <?php echo (isset($gender) && $gender == 'male') ? 'selected' : ''; ?>>Male</option>
            <option value="female" <?php echo (isset($gender) && $gender == 'female') ? 'selected' : ''; ?>>Female</option>
            <option value="other" <?php echo (isset($gender) && $gender == 'other') ? 'selected' : ''; ?>>Other</option>
        </select>
                 
            </div>
            <div class="form-section">
                <label for="marital_status">Marital Status:</label>
                <select id="marital_status" name="marital_status" required>
                    <option value="married">Married</option>
                    <option value="divorced">Divorced</option>
                    <option value="unmarried">Unmarried</option>
                </select>
            </div>
            <div class="form-section">
                <label for="nationality">Nationality:</label>
                <input type="text" id="nationality" name="nationality" value="<?php echo isset($nationality) ? $nationality : ''; ?>" required><br><br>
                </div>
            <div class="form-section">
                <label for="state">State:</label>
                <input type="text" id="state" name="state" value="<?php echo isset($state) ? $state : ''; ?>" required><br><br>
                </div>
            <div class="form-section">
                <label for="mothers_name">Mother's Name:</label>
                <input type="text" id="mothers_name" name="mothers_name" value="<?php echo isset($mothers_name) ? $mothers_name : ''; ?>" required><br><br>
                </div>
            <div class="form-section">
                <label for="fathers_name">Father's Name:</label>
                <input type="text" id="fathers_name" name="fathers_name" value="<?php echo isset($fathers_name) ? $fathers_name : ''; ?>" required><br><br>
                </div>
            <div class="form-section">
                <label for="alternate_mobile">Alternate Mobile No:</label>
                <input type="text" id="alternate_mobile" name="alternate_mobile" value="<?php echo isset($alternate_mobile) ? $alternate_mobile : ''; ?>"><br><br>
                </div>
                <div class="form-section">
                <label for="category">Category:</label>
                <select name="category" id="category" onchange="toggleCategoryCertificate()" required>
                    <option value="general" <?php echo (isset($category) && $category == 'general')? 'selected' : '';?>>General</option>
                    <option value="SC" <?php echo (isset($category) && $category == 'SC')? 'selected' : '';?>>SC</option>
                    <option value="ST" <?php echo (isset($category) && $category == 'ST')? 'selected' : '';?>>ST</option>
                    <option value="OBC" <?php echo (isset($category) && $category == 'OBC')? 'selected' : '';?>>OBC</option>
                </select>
                <br>
                <div id="category_certificate_div" style="display: <?php echo (isset($category) && ($category!= 'general'))? 'block' : 'none';?>">
                    <label for="category_certificate">Upload Category Certificate:</label>
                    <input type="file" id="category_certificate" name="category_certificate">
                    <input type="button" class="save-button" value="Upload" onclick="uploadFile('category_certificate', 'category_certificate')">
                    <div class="form-section">
    <input type="button" value="View Uploaded Document " onclick="viewCategory()">
</div>

                </div>
            </div>
            
            <div class="form-section">
    <label for="ex_serviceman">Are you an ex-serviceman?</label>
    <select id="ex_serviceman" name="ex_serviceman" onchange="toggleExServicemanFields()">
        <option value="no">No</option>
        <option value="yes">Yes</option>
    </select>
    <br>
    <div id="ex_serviceman_details" style="display: none;">
        <div class="form-section">
            <label for="date_of_retirement">Date of Retirement:</label>
            <input type="date" id="date_of_retirement" name="date_of_retirement">
        </div>
        <div class="form-section">
            <label for="service_no">Service No:</label>
            <input type="text" id="service_no" name="service_no">
        </div>
        <div id="specify_ex_serviceman" style="display: none;">
            <label for="specify_ex_serviceman">Specify:</label>
            <input type="text" id="specify_ex_serviceman" name="specify_ex_serviceman">
        </div>
    </div>
</div>
<div class="form-section">
    <label for="differently_abled">Are you differently abled?</label>
    <select id="differently_abled" name="differently_abled" onchange="toggleDisability()">
        <option value="no" <?php echo (isset($differently_abled) && $differently_abled == 'no') ? 'selected' : ''; ?>>No</option>
        <option value="yes" <?php echo (isset($differently_abled) && $differently_abled == 'yes') ? 'selected' : ''; ?>>Yes</option>
    </select>
</div>
<div class="form-section" id="disability_section" style="display: <?php echo (isset($differently_abled) && $differently_abled == 'yes') ? 'block' : 'none'; ?>">
    <label for="disability_percentage">Disability Percentage:</label>
    <select id="disability_percentage" name="disability_percentage">
        <option value="20">20%</option>
        <option value="30">30%</option>
        <option value="40">40%</option>
        <option value="50">50%</option>
        <option value="60">60%</option>
        <option value="70">70%</option>
        <option value="80">80%</option>
        <option value="90">90%</option>
    </select>
    <br>
    <label for="disability_type">Disability Type:</label>
    <input type="text" name="disability_type" value="<?php echo isset($disability_type) ? $disability_type : ''; ?>"><br><br>
    <label for="disability_certificate">Upload Disability Certificate:</label>
    <input type="file" id="disability_certificate" name="disability_certificate">
    <input type="button" class="save-button" value="Upload" onclick="uploadFiledis('disability_certificate', 'disability_certificate')">
    <input type="button" value="View Uploaded Document " onclick="viewdisability()">


</div>
 
            <div class="form-section">
                <label for="corrospondance_address">corrospondance Address:</label>
                <textarea id="corrospondance_address" name="corrospondance_address" rows="4" required><?php echo isset($corrospondance_address) ? $corrospondance_address : ''; ?></textarea><br><br>
                </div>
            <div class="form-section">
                <label for="permanent_address">Permanent Address:</label>
                <textarea id="permanent_address" name="permanent_address" rows="4" required><?php echo isset($permanent_address) ? $permanent_address : ''; ?></textarea><br><br>
                </div>
            <div class="form-section">
                <label for="corrospondance_city">corrospondance City Name:</label>
                <input type="text" id="corrospondance_city" name="corrospondance_city" value="<?php echo isset($corrospondance_city) ? $corrospondance_city : ''; ?>" required><br><br>
                </div>
            <div class="form-section">
                <label for="permanent_city">Permanent City Name:</label>
                <input type="text" id="permanent_city" name="permanent_city" value="<?php echo isset($permanent_city) ? $permanent_city : ''; ?>" required><br><br>
                </div>
            <form id="personal_details_form" action="index.php" method="post">
     
       
    

      

         
    <div class="form-section">
         <input type="submit" class="submit" name="submit" value="submit  "  >
         <input type="button" class="save-button" value="Back" onclick="goBack()">

     </div>
</form>
        </form>
    </div>

    <script>



        

        function showSpecifyTextBox(elementId, targetId) {
            var dropdown = document.getElementById(elementId);
            var target = document.getElementById(targetId);

            if (dropdown.value === 'other') {
                target.style.display = "block";
            } else {
                target.style.display = "none";
            }
        }

      

         

        document.getElementById("ex_serviceman").addEventListener("change", function() {
            showSpecifyTextBox("ex_serviceman", "specify_ex_serviceman");
        });

        function logoutUser() {
            window.location.href = 'logout.php';  
        }

         
    </script>
 <script>
        function toggleDisability() {
            var differently_abled = document.getElementById("differently_abled").value;
            var disability_section = document.getElementById("disability_section");
            var disability_certificate_section = document.getElementById("disability_certificate_section");

            if (differently_abled === 'yes') {
                disability_section.style.display = "block";
                disability_certificate_section.style.display = "block";
            } else {
                disability_section.style.display = "none";
                disability_certificate_section.style.display = "none";
            }
        }

        

        function logoutUser() {
            // Implement logout functionality here
        }

// Function to toggle visibility of certificate upload field based on category selection
function toggleCategoryCertificate() {
            var category = document.getElementById('category').value;
            var categoryCertificateDiv = document.getElementById('category_certificate_div');
            categoryCertificateDiv.style.display = (category === 'SC' || category === 'ST' || category === 'OBC') ? 'block' : 'none';
        }

    // Event listener for category dropdown change
    document.getElementById("category").addEventListener("change", function() {
        toggleCertificateUpload(); // Call function when category selection changes
    });

    // Initial call to set visibility based on current category selection on page load
    toggleCertificateUpload();

function toggleExServicemanFields() {
    var ex_serviceman = document.getElementById("ex_serviceman").value;
    var ex_serviceman_details = document.getElementById("ex_serviceman_details");
    var specify_ex_serviceman = document.getElementById("specify_ex_serviceman");

    if (ex_serviceman === 'yes') {
        ex_serviceman_details.style.display = "block";
        specify_ex_serviceman.style.display = "block";
    } else {
        ex_serviceman_details.style.display = "none";
        specify_ex_serviceman.style.display = "none";
    }
}

// Event listener for the ex_serviceman dropdown change
document.getElementById("ex_serviceman").addEventListener("change", function() {
    toggleExServicemanFields(); 
});



    </script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
