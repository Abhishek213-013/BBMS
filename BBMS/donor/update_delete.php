<?php

    declare(strict_types=1);
    require_once("../includes/dbh.inc.php");
    require_once("../includes/session.inc.php");

    if($_SERVER['REQUEST_METHOD']=="POST")
    {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $username = $_POST['username'];
        $latitude = $_POST["latitude"];
        $longitude = $_POST["longitude"];

        try {
            // Fetch donor ID
            $query = "SELECT id FROM donor WHERE username=:current_username;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":current_username", $_SESSION['donor']);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $donor_id = $result["id"];

            $errors = [];

            // Validate form fields
            if(empty($username) || empty($email) || empty($name)) {
                $errors["donor_error_profile"] = "Fill all fields!";
            }
            if(username_exists($pdo, $username, $donor_id)) {
                $errors["user_exists"] = "User already exists!";
            }
            if(email_exists($pdo, $email, $donor_id)) {
                $errors["email_exists"] = "Email already exists!";
            }

            // Handle errors
            if($errors) {
                $_SESSION["donor_error_profile"] = $errors;
                header("Location: dashboard.php?profile=1");
                die();
            }

            // Update profile
            if(isset($_POST['update'])) {
                $query = "UPDATE donor SET username=:username, email=:email, name=:name, latitude=:latitude, longitude=:longitude WHERE id=:id;";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":email", $email);
                $stmt->bindParam(":username", $username);
                $stmt->bindParam(":latitude", $latitude);
                $stmt->bindParam(":longitude", $longitude);
                $stmt->bindParam(":id", $donor_id);
                $stmt->execute();
    
                $_SESSION['donor'] = $username;
                header('Location: dashboard.php?profile=1');
            }
            // Delete account
            else if(isset($_POST['delete'])) {
                $query = "DELETE FROM donor WHERE id=:id;";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":id", $donor_id);
                $stmt->execute();
                
                header('Location: dashboard.php?logout=1');
            }

            $pdo = null;
            $stmt = null;

            die();

        } catch (PDOException $e) {
            echo $e->getMessage();
        }

    } else {
        header("Location: dashboard.php");
        die();
    }

    function username_exists(object $pdo, string $username, int $id)
    {
        $query = "SELECT username FROM donor WHERE username=:username AND id!=:id;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? true : false;
    }

    function email_exists(object $pdo, string $email, int $id)
    {
        $query = "SELECT email FROM donor WHERE email=:email AND id!=:id;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? true : false;
    }
?>
