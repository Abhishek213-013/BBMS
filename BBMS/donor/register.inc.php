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
        // Errors array
        $errors = [];

        // Validate inputs
        if (checkInput($name, $email, $pwd, $username, $blood)) {
            $errors["check_input"] = "All fields are required!";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["invalid_email"] = "Invalid email format!";
        }
        if (strlen($pwd) < 8) {
            $errors["weak_password"] = "Password must be at least 8 characters!";
        }
        if (username_exists($pdo, $username)) {
            $errors["user_exists"] = "This username is already taken!";
        }
        if (email_exists($pdo, $email)) {
            $errors["email_exists"] = "This email is already registered!";
        }

        // If there are errors, redirect back to register page
        if ($errors) {
            $_SESSION["donor_error_register"] = $errors;
            header("Location: register.php");
            die();
        }

        // Insert user into database
        insert_user($pdo, $name, $username, $pwd, $email, $blood, $latitude, $longitude);

        // Set session variable for logged-in user
        $_SESSION["donor"] = $username;

        // Redirect to success page
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
    $query = "SELECT username from donor where username=:username;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return (bool) $result;
}

function email_exists(object $pdo, string $email)
{
    $query = "SELECT email from donor where email=:email;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return (bool) $result;
}

function insert_user(object $pdo, string $name, string $username, string $pwd, string $email, string $blood, $latitude, $longitude)
{
    $query = "INSERT INTO donor (name, username, pwd, email, blood, latitude, longitude) 
              VALUES (:name, :username, :pwd, :email, :blood, :latitude, :longitude);";
    $stmt = $pdo->prepare($query);
    $options = ["cost" => 10];
    $hashedPwd = password_hash($pwd, PASSWORD_BCRYPT, $options);
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":pwd", $hashedPwd);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":blood", $blood);
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":latitude", $latitude);
    $stmt->bindParam(":longitude", $longitude);
    $stmt->execute();
}
?>
