<?php
    require_once("../includes/session.inc.php");
    require_once("../includes/dbh.inc.php");
    require_once("../includes/template.php");
    if (!isset($_SESSION["donor"])) {
        header("Location: login.php");
        die();
    }

    if (!isset($_GET['home']) && !isset($_GET["profile"]) && !isset($_GET["donate_blood"]) && !isset($_GET["donations_history"]) && !isset($_GET["logout"])) {
        header('Location:dashboard.php?home=1');
    }

    if (isset($_GET["logout"])) {
        unset($_SESSION["donor"]);
        session_destroy();
        header("Location:../index.php");
        die();
    }

    function print_error(string $error)
    {
        echo '<div class="alert alert-danger alert-dismissible fade show text-center mx-auto" role="alert" style="width: fit-content;">';
        echo $error;
        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </div>
        ';
    }

    function check_errors()
    {
        if(isset($_SESSION["donor_error_donate"]))
        {
            $errors = $_SESSION["donor_error_donate"];
            foreach ($errors as $error) {
                print_error($error);
            }
            unset($_SESSION["donor_error_donate"]);
        }
    }

    function check_profile_errors()
    {
        if(isset($_SESSION["donor_error_profile"]))
        {
            $errors = $_SESSION["donor_error_profile"];
            foreach ($errors as $error) {
                print_error($error);
            }
            unset($_SESSION["donor_error_profile"]);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="shortcut icon" href="../images/blood-drop.svg" type="image/x-icon">
    <style>
        html, body {
            min-height: 100%;
            margin: 0;
            padding: 0;
        }

        .navbar-nav .nav-item a , .dropdown a {
            position: relative;
            color: #777;
            text-transform: uppercase;
            margin-right: 10px;
            text-decoration: none;
            overflow: hidden;
        }

        .dropdown-menu , .dropdown-menu a:hover {
            background-color: #ffffff;
        }

        .navbar-nav  li a:hover , .dropdown a:hover {
            color: #1abc9c !important;
        }

        /* Dark Mode Styling */
        body.dark-mode {
            background-color: #121212;
            color: #ffffff;
        }
        body.dark-mode .navbar-light {
            background-color: #333;
        }
        body.dark-mode .navbar-nav .nav-item a {
            color: #fff;
        }
        body.dark-mode .navbar-nav .nav-item a:hover {
            color: #1abc9c;
        }
        .dark-mode-toggle {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container" style="margin-bottom: 100px;">
        <nav class="navbar navbar-expand-lg navbar-light fixed-top navbar-shading" style="background-color:#660000;">
            <a class="navbar-brand" href="../index.php" style="color: #ffffff;font-size:22px;letter-spacing:2px;">BBMS</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="?home=1" style="color: #ffffff">Home</a>
                    </li>
                    <li>
                        <?php
                        echo 
                        '
                        <div class="dropdown">
                            <a class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding-left:0px;">
                                '.$_SESSION['donor'].'
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                <li>
                                    <a class="dropdown-item" href="?profile=1">Profile</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="?logout=1">Logout</a>
                                </li>
                            </ul>
                        </div>
                        ';
                        ?>
                    </li>
                    <!-- Dark Mode Toggle Button -->
                    <li class="nav-item dark-mode-toggle">
                        <button id="darkModeToggle" class="btn btn-secondary">Dark Mode</button>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <script>
        const darkModeToggle = document.getElementById("darkModeToggle");
        darkModeToggle.addEventListener("click", () => {
            const body = document.body;
            body.classList.toggle("dark-mode");

            if (body.classList.contains("dark-mode")) {
                darkModeToggle.textContent = "Light Mode";
            } else {
                darkModeToggle.textContent = "Dark Mode";
            }
        });
    </script>

    <?php
        if(isset($_GET))
        {
            if(count($_GET) > 1)
            {
                print_error("Link Corrupted!! Correct the link.......");

            }
            else
            {
                $getOne = key($_GET);
            }
        }

        if ($getOne && $getOne==='home')
        {
            if (!isset($_SESSION["welcome_donor_message"])) {
                echo '<div class="alert alert-success alert-dismissible fade show text-center mx-auto" role="alert" style="width: fit-content;">
                        Welcome, ' . $_SESSION["donor"]. '
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                $_SESSION["welcome_donor_message"]=true;
            }

            $val = reset($_GET);

            if($val!=='1') 
            {
                print_error("Link Corrupted!! Correct the link.......");
                die();
            }

            $input = [
                "Donor",
                "Donate",
                "Make a new blood donation appointment.",
                "donate",
                "Donation",
                "View your past blood donation records.",
                "donations"
            ];

            home_template($input);
        }
        else if ($getOne && $getOne==='profile')
        {
            $val = reset($_GET);

            if($val!=='1') 
            {
                print_error("Link Corrupted!! Correct the link.......");

            }

            check_profile_errors();

            $query = "SELECT * from donor where username=:username;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":username",$_SESSION["donor"]);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            profile_template($row,'Donor');
        }
        else if ($getOne && $getOne==='donate_blood')
        {
            $val = reset($_GET);

            if($val!=='1') 
            {
                print_error("Link Corrupted!! Correct the link.......");

            }

            check_errors();

            donate_request_template("donate.php","Donate Blood","Disease","disease","Donate");
        }
        else if ($getOne && $getOne==='donations_history')
        {
            $val = reset($_GET);

            if($val!=='1') 
            {
                print_error("Link Corrupted!! Correct the link.......");

            }

            $query = "SELECT id from donor where username=:current_username;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":current_username", $_SESSION['donor']);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $donor_id = $result["id"];

            $query = "SELECT * from donate where donor_id=:id order by id desc;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id",$donor_id);
            $stmt->execute();

            $cnt=0;

            echo '<div class="container mt-5 mb-5">
                    <h2 class="text-center mb-4">Donation History</h2>
                    <div class="row align-items-center">';

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                history_template($row,"Disease","disease");

                $cnt++;
            }

            echo '</div>
            </div>';

            if($cnt==0) print_error("No History Found!!!");

        }
        else{
            print_error("Link Corrupted!! Correct the link.......");
        }
    ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
