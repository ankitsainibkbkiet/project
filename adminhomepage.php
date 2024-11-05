<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page - CEERI PILANI Website</title>
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
            padding-top: 70px; /* Adjust to fit the navbar height */
        }

        .container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5px; /* Reduced gap between both columns and rows */
            width: 80%;
            max-width: 600px;
        }

        .tile {
            background-color: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            width: 90%;
            padding-top: 50%; /* 1:1 Aspect Ratio */
        }

        .tile a {
            text-decoration: none;
            color: inherit;
        }

        .tile:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            cursor: pointer;
        }

        .tile h2 {
            margin: 0;
            font-size: 1rem;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body>
    <?php include('navnew.php'); ?>

    <div class="container">
        <div class="tile">
            <a href="broadcast.php">
                <h2>Broadcast a Post</h2>
            </a>
        </div>
        <div class="tile">
            <a href="view_all_applications.php">
                <h2>View All Applications</h2>
            </a>
        </div>
        <div class="tile">
            <a href="view_shortlisted_applications.php">
                <h2>View Shortlisted Applications</h2>
            </a>
        </div>
        <div class="tile">
            <a href="view_rejected_applications.php">
                <h2>View Rejected Applications</h2>
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
