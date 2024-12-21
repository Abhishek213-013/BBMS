<?php
    
    require_once("../includes/session.inc.php");
    require_once("../includes/template.php");
    if(isset($_SESSION["patient"]) && isset($_GET["register"]) && $_GET["register"]==="success")
    {
        header("Location:dashboard.php");
    }

    function check_errors()
    {
        if(isset($_SESSION["patient_error_register"]))
        {
            $errors = $_SESSION["donor_error_register"];
            echo "<br>";
            foreach ($errors as $error) {
                echo '<div class="alert alert-danger alert-dismissible fade show text-center mx-auto" role="alert" style="width: fit-content;">';
                echo $error;
                echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close" style="background:none;">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>
                ';
            }
            unset($_SESSION["patient_error_register"]);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Register</title>
    <!-- Include Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Favicon -->
    <link rel="shortcut icon" href="../images/blood-drop.svg" type="image/x-icon">
    <!-- Apply custom styles for the form -->
    <style>
        html, body {
            min-height: 100%;
            margin: 0;
            padding: 0;
            transition: background-color 0.3s, color 0.3s;
        }
        .form-container {
            border-radius: 10px;
            padding: 20px;
            margin: 10px auto 50px;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .active , .active:hover {
            background-color: #1abc9c; /* Highlight color for the active button */
            color:#fff;
        }
        .btn {
            border: 1px #1ac9bc solid;
            margin: 5px;
        }
        .custom-text-center {
            padding: 10px;
            max-width: 400px;
            margin: auto;
        }
        @media (min-width: 576px) {
            .text-center {
                display: flex;
                justify-content: center;
            }
            .btn {
                flex: 1;
            }
        }

        /* Light Mode Styles */
        body {
            background-color: #fff; /* White background in light mode */
            color: #000;
        }
        .navbar {
            background-color: #660000; /* Blood red color for navbar in light mode */
        }
        .btn {
            background-color: #660000; /* Blood red color for buttons in light mode */
            color: white;
        }

        /* Dark Mode Styles */
        body.dark-mode {
            background-color: #121212; /* Black background in dark mode */
            color: white;
        }

        body.dark-mode .navbar {
            background-color: #000; /* Black color for navbar in dark mode */
        }

        body.dark-mode .btn {
            background-color: #000; /* Black color for buttons in dark mode */
            color: white;
        }

    </style>
</head>
<body>
    <div class="container" style="margin-top:80px;">
        <nav class="navbar navbar-expand-lg navbar-light fixed-top">
            <a class="navbar-brand" href="../index.php" style="color: #fff;font-size:22px;letter-spacing:2px;">BBMS</a>
        </nav>
        
        <?php 
            check_errors();
        ?>

        <!-- Button to toggle Dark Mode -->
        <button onclick="toggleDarkMode()" class="btn btn-secondary">Toggle Dark Mode</button>

        <div class="text-center custom-text-center">
            <a class="btn" href="../patient/register.php">As Patient</a>
            <a class="btn active" href="../donor/register.php">As Donor</a>
        </div>

        <!-- Patient Register Form -->
        <form action="register.inc.php" method="POST" class="form-container">
            <h3 class="text-center">Patient Register</h3>

            <!-- Name -->
            <input type="text" class="form-control" name="name" placeholder="Full Name" required>

            <!-- Email -->
            <input type="email" class="form-control" name="email" placeholder="Email" required>

            <!-- Username -->
            <input type="text" class="form-control" name="username" placeholder="Username" required>

            <!-- Password -->
            <input type="password" class="form-control" name="pwd" placeholder="Password" required>

            <!-- Blood Type -->
            <select name="blood" class="form-control" required>
                <option value="A+">A+</option>
                <option value="B+">B+</option>
                <option value="O+">O+</option>
                <option value="AB+">AB+</option>
                <option value="A-">A-</option>
                <option value="B-">B-</option>
                <option value="O-">O-</option>
                <option value="AB-">AB-</option>
            </select>

            <!-- Hidden Fields for Location -->
            <input type="hidden" id="latitude" name="latitude" value="">
            <input type="hidden" id="longitude" name="longitude" value="">

            <!-- Register Button -->
            <button type="submit" class="btn btn-success btn-block">Register</button>
        </form>
    </div>

    <!-- Include Bootstrap JS and jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Function to get user's location using Geolocation API
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    document.getElementById("latitude").value = latitude;
                    document.getElementById("longitude").value = longitude;
                }, function(error) {
                    alert("Unable to retrieve location.");
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        // Automatically get the location when the page is loaded
        window.onload = function() {
            getLocation();
        };

        // Function to toggle dark mode
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }
    </script>
</body>
</html>
