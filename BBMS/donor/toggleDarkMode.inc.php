<?php
session_start();
include_once 'dbh.inc.php';

if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
    
    // Fetch current dark mode setting
    $sql = "SELECT dark_mode FROM donor WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "SQL error";
    } else {
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $darkMode = $row['dark_mode'];
            $newDarkMode = $darkMode ? 0 : 1;
            
            // Update dark mode setting
            $sql = "UPDATE donor SET dark_mode = ? WHERE id = ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {
                echo "SQL error";
            } else {
                mysqli_stmt_bind_param($stmt, "ii", $newDarkMode, $userId);
                mysqli_stmt_execute($stmt);
                header("Location: ../dashboard.php");
                exit();
            }
        }
    }
}
?>
