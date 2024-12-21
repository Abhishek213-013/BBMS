<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BBMS</title>
    <!-- Include Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Favicon -->
    <link rel="shortcut icon" href="images/blood-drop.svg" type="image/x-icon">
    <style>
    html {
        min-height: 100%;
        position: relative;
    }

    /* Default styles */
    body {
        background-color: #ffffff; /* Light background for light mode */
        color: #000000; /* Black text in light mode */
    }

    .navbar {
        background-color: #660000; /* Navbar background color */
    }

    .footer {
        background-color: #660000; /* Footer background for light mode */
        color: #FFF;
    }

    .main-container {
        background-color: #ffffff; /* Default white background */
        color: #000000; /* Default black text */
        padding: 20px;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    /* Dark mode styles */
    body.dark-mode {
        background-color: #1e1e1e; /* Dark background for the body */
        color: #ffffff; /* White text in dark mode */
    }

    .navbar.dark-mode {
        background-color: #333333 !important;
    }

    .footer.dark-mode {
        background-color: #000000;
        color: #FFD700;
    }

    .main-container.dark-mode {
        background-color: #2c2c2c; /* Dark background for main container */
        color: #ffffff; /* White text for main container in dark mode */
    }

    .dark-mode-btn {
        cursor: pointer;
        background-color: transparent;
        border: none;
        color: #FFF;
        font-size: 16px;
        margin-left: 10px;
    }

    .dark-mode-btn:hover {
        color: #FFD700;
    }
    </style>
    <style>
    /* Additional styling for navbar links and buttons */
    .navbar-nav .nav-item {
        display: flex;
        align-items: center; /* Vertically align items */
    }

    .dark-mode-btn {
        cursor: pointer;
        background-color: transparent;
        border: 1px solid #fff;
        color: #fff;
        font-size: 16px;
        padding: 5px 10px;
        border-radius: 5px;
        margin-left: 15px; /* Adds space between the button and the previous link */
    }

    .dark-mode-btn:hover {
        color: #FFD700;
        border-color: #FFD700;
    }
</style>

</head>
<body>
    <!-- Bootstrap navigation bar -->
    <div class="container" style="margin-bottom: 50px;">
        <nav class="navbar navbar-expand-lg navbar-light fixed-top">
            <a class="navbar-brand" href="index.php" style="color: #FFF; font-size: 22px; letter-spacing: 2px;">BBMS</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="patient/register.php" style="color: #FFF;">REGISTER</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="patient/login.php" style="color: #FFF;">LOGIN</a>
                    </li>
                    <li class="nav-item">
                        <button id="dark-mode-btn" class="dark-mode-btn">Dark Mode</button>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <div id="main-container" class="container text-center main-container" style="padding-top: 100px; padding-bottom: 50px;">
        <h1 class="display-6">Blood Bank Management System</h1>
        <div class="row align-items-center">
            <div class="col-lg-6">
                <p class="lead mt-3">
                    This system is designed to efficiently manage blood donations, donors, and recipients, ensuring the availability of safe and life-saving blood for those in need.
                </p>
                <p class="lead mt-3 mb-5">
                    Join us in the mission to save lives. Register as a donor or recipient and help make a difference!
                </p>
            </div>
            <div class="col-lg-6">
                <img id="animated-image" src="images/home.svg" alt="" class="img-fluid d-none d-lg-block">
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS and jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Dark mode toggle functionality
        const darkModeButton = document.getElementById('dark-mode-btn');
        darkModeButton.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            document.querySelector('.navbar').classList.toggle('dark-mode');
            const footer = document.querySelector('.footer');
            footer.classList.toggle('dark-mode');
            const mainContainer = document.getElementById('main-container');
            mainContainer.classList.toggle('dark-mode');
            darkModeButton.textContent =
                darkModeButton.textContent === 'Dark Mode' ? 'Light Mode' : 'Dark Mode';
        });
    </script>
</body>
<footer class="footer" style="padding: 15px; text-align: center; position: absolute; bottom: 0; width: 100%;">
    &copy; <a style="color: #FFF;" href="https://github.com/Abhishek213-013">Abhishek</a> and <a style="color: #FFF;" href="https://github.com/riad535">Riad</a> 
</footer>
</html>
