<?php

declare(strict_types=1);
require_once("../includes/session.inc.php");
require_once("../includes/dbh.inc.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Retrieving POST data
        $disease = $_POST["disease"];
        $unit = $_POST["unit"];
        
        $errors = [];

        // Validations
        if (empty($disease) || $unit == null) {
            $errors["donate_empty"] = "Fill all fields!";
        }
        if ($unit && $unit < 0) {
            $errors["donate_negative"] = "Blood units cannot be negative!";
        }

        // If there are validation errors, redirect back with error messages
        if ($errors) {
            $_SESSION["donor_error_donate"] = $errors;
            header("Location: dashboard.php?donate_blood=1");
            die();
        }

        // Ensure the user is logged in
        if (empty($_SESSION['donor'])) {
            echo "No donor is logged in.";
            exit; // Or redirect to login page
        }

        // Fetch donor's blood type
        $query = "SELECT blood FROM donor WHERE username = :current_username;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":current_username", $_SESSION['donor']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the blood data exists
        if ($result) {
            $blood = $result["blood"];
        } else {
            echo "No donor found.";
            exit; // Or handle the error as needed
        }

        // Fetch donor's ID
        $query = "SELECT id FROM donor WHERE username = :current_username;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":current_username", $_SESSION['donor']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the donor ID exists
        if ($result) {
            $donor_id = $result["id"];
        } else {
            echo "No donor ID found.";
            exit; // Or handle the error as needed
        }

        // Insert donation record
        $query = "INSERT INTO donate (username, donor_id, disease, blood, unit) 
                  VALUES (:current_username, :id, :disease, :blood, :unit);";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":current_username", $_SESSION["donor"]);
        $stmt->bindParam(":disease", $disease);
        $stmt->bindParam(":blood", $blood);
        $stmt->bindParam(":id", $donor_id);
        $stmt->bindParam(":unit", $unit);
        $stmt->execute();

        // Redirect to donation history page
        header("Location: dashboard.php?donations_history=1");

        // Close database connections
        $pdo = null;
        $stmt = null;

        die();
    } catch (PDOException $e) {
        // Display the error message if a database issue occurs
        echo "Error: " . $e->getMessage();
    }
} else {
    // Redirect if the form is not submitted via POST
    header("Location: dashboard.php");
    die();
}

?>
