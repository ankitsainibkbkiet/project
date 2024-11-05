<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .navbar-logo {
            max-height: 50px; /* Adjust this value based on your preferred logo size */
            width: auto; /* Maintain aspect ratio */
            margin-right: 10px; /* Space between logo and text */
        }

        .navbar, .dropdown-menu {
            background-color: navy;
        }

        .navbar-nav .nav-link {
            color: white !important;
            margin: 0 10px;
            font-size: 16px;
        }

        .navbar-nav .nav-link:hover {
            color: lightgray !important;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            min-width: 160px;
            z-index: 1;
            display: none; /* Hide by default */
        }

        .dropdown-menu a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
            transition: background-color 0.3s;
        }

        .dropdown-menu a:hover {
            background-color: lightgray;
            color: black;
        }

        .nav-item.dropdown:hover .dropdown-menu {
            display: block; /* Show on hover */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <a class="navbar-brand" href="login.php">
            <img src="https://www.ceeri.res.in/wp-content/uploads/2023/06/blue_filled_final_logo_PS_vfinal-300x294.png" alt="CEERI Logo" class="navbar-logo">
            CEERI PILANI
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="login.php">HOME</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">RESULT</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">SHORTLISTED CANDIDATES</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="advertisementDropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        Advertisement No
                    </a>
                    <div class="dropdown-menu" aria-labelledby="advertisementDropdown">
                        <a class="dropdown-item" href="#">adv_101</a>
                        <a class="dropdown-item" href="#">adv_102</a>
                        <a class="dropdown-item" href="#">adv_103</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="aboutCeeriDropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        About
                    </a>
                    <div class="dropdown-menu" aria-labelledby="aboutCeeriDropdown">
                        <a class="dropdown-item" href="https://maps.app.goo.gl/RWM7qZzvgvXbgLAdA">Location and Map</a>
                        <a class="dropdown-item" href="https://www.churchleadership.com/wp-content/uploads/2021/03/how-to-stay-focused-on-your-mission.png" target="_blank">Mission</a>
                        <a class="dropdown-item" href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS8IkREmNFC062bs_TBftcb_8xbnYNa8LYHVA&s" target="_blank">History</a>
                        <a class="dropdown-item" href="https://www.linkedin.com/in/pc-panchariya-95583674/?originalSubdomain=in">Director</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="phone.php" id="contactUsDropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        Contact Us
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Add the necessary JavaScript for Bootstrap's dropdown to function -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
