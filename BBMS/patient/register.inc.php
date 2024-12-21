<?php 

    declare(strict_types= 1);
    require_once("../includes/dbh.inc.php");
    require_once("../includes/session.inc.php");

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $pwd = $_POST["pwd"];
        $username = $_POST["username"];
        $blood = $_POST["blood"];
        $latitude = $_POST["latitude"] ?? null;  // Latitude
        $longitude = $_POST["longitude"] ?? null;  // Longitude

        try {
            // errors
            $errors = [];

            if (checkInput($name, $email, $pwd, $username, $blood)) {
                $errors["check_input"] = "Fill all fields!";
            }
            if (username_exists($pdo, $username)) {
                $errors["user_exists"] = "User already exists!";
            }
            if (email_exists($pdo, $email)) {
                $errors["email_exists"] = "Email already exists!";
            }

            if ($errors) {
                $_SESSION["donor_error_register"] = $errors;
                header("Location: register.php");
                die();
            }

            insert_user($pdo, $name, $username, $pwd, $email, $blood, $latitude, $longitude);

            $_SESSION["patient"] = $username;

            header("Location: register.php?register=success");

            $pdo = null;
            $stmt = null;

            die();
        } catch (PDOException $e) {
            // Handle exception
            die("Query failed: " . $e->getMessage());
        }

    } else {
        header("Location: register.php");
        die();
    }

    function checkInput(string $name, string $email, string $pwd, string $username, string $blood)
    {
        return empty($name) || empty($email) || empty($pwd) || empty($username) || empty($blood);
    }

    function username_exists(object $pdo, string $username)
    {
        $query = "SELECT username from patient where username=:username;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) return true;
        return false;
    }

    function email_exists(object $pdo, string $email)
    {
        $query = "SELECT email from patient where email=:email;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) return true;
        return false;
    }

    function insert_user(object $pdo, string $name, string $username, string $pwd, string $email, string $blood, $latitude, $longitude)
    {
        $query = "INSERT INTO patient (name, username, pwd, email, blood, latitude, longitude) 
                  VALUES (:name, :username, :pwd, :email, :blood, :latitude, :longitude);";   
        $stmt = $pdo->prepare($query);
        $options = [
            "cost" => 10
        ];
        $hashedPwd = password_hash($pwd, PASSWORD_BCRYPT, $options);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":pwd", $hashedPwd);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":blood", $blood);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":latitude", $latitude);  // Bind latitude
        $stmt->bindParam(":longitude", $longitude);  // Bind longitude
        $stmt->execute();
    }
?>