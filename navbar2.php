<?php
$isExperienceButtonEnabled = isset($_SESSION['isnextenabled']) && $_SESSION['isnextenabled'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .left-navbar {
            width: 250px;
            position: fixed;
            top: 40px; /* Adjust based on the height of the top navbar */
            left: 0;
            height: 100%;
            background-color: navy; /* Navy blue */
            color: #fff;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            padding-bottom: 20px; /* Increased height */
        }

        .left-navbar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .left-navbar ul li {
            margin: 15px 0; /* Adjusted spacing */
        }

        .left-navbar ul li a,
        .dropdown-item {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 15px 20px; /* Increased padding for height */
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .left-navbar ul li a:hover,
        .dropdown-item:hover {
            background-color: #002244; /* Darker navy blue for hover effect */
        }

        .dropdown-menu {
            background-color: #003366; /* Navy blue */
            color: #fff;
            border: none;
            box-shadow: none;
        }

        .disabled-link {
            pointer-events: none;
            opacity: 0.6;
        }
    </style>
</head>
<body>
     
    <nav class="left-navbar">
        <ul>
            <li><a href="persdet.php">Personal Details</a></li>
            <li><a href="eduquli.php">Education Qualification</a></li>
            <li>
                <?php if ($isExperienceButtonEnabled): ?>
                    <a href="experience.php">Experience</a>
                <?php else: ?>
                    <a href="#" class="disabled-link">Experience</a>
                <?php endif; ?>
            </li>
            <li><a href="candiupload.php">Upload Files</a></li>
            <li><a href="printable.php">Preview Application</a></li>
             <!-- Dropdown list item 
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    More Options
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="option1.php">Option 1</a>
                    <a class="dropdown-item" href="option2.php">Option 2</a>
                    <a class="dropdown-item" href="option3.php">Option 3</a>
                </div>
            </li>-->
        </ul>
    </nav>

    <!-- Add the necessary JavaScript for Bootstrap's dropdown to function -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
