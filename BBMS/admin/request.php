<?php
declare(strict_types=1);

require_once("../includes/dbh.inc.php");
require_once("../includes/session.inc.php");

if (!isset($_SESSION["admin"])) {
    // Redirect if the admin is not logged in
    header("Location: login.php");
    exit();
}

try {
    // Handle approve or reject actions
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Check if the 'patient_id' exists in the POST request
        if (isset($_POST["patient_id"]) && (isset($_POST["approve"]) || isset($_POST["reject"]))) {
            $id = $_POST['patient_id'];
            $input_status = isset($_POST['approve']) ? "approved" : "rejected";

            // Fetch the request details
            $query = "SELECT * FROM request WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if the result is valid
            if ($result === false) {
                echo "Request not found.";
                exit();
            }

            if ($input_status == "approved") {
                // Handle blood unit update for approved request
                $unit = $result["unit"];
                $blood = $result["blood"];

                // Normalize the blood type for consistent storage in the database
                $blood_mapping = [
                    "A+" => "AP", "A-" => "AN", "B+" => "BP", "B-" => "BN",
                    "AB+" => "ABP", "AB-" => "ABN", "O+" => "OP", "O-" => "ON"
                ];

                $blood = isset($blood_mapping[$blood]) ? $blood_mapping[$blood] : $blood;

                // Check if unit is not NULL before updating the blood quantity
                if ($unit !== null && $unit > 0) {
                    // Update the blood quantity in the blood table by subtracting the requested unit
                    $blood_id = 1; // Assuming 1 is the correct ID for the blood record
                    $query = "UPDATE blood SET {$blood} = {$blood} - :unit WHERE id = :id"; // Subtract units
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(":id", $blood_id, PDO::PARAM_INT);
                    $stmt->bindParam(":unit", $unit, PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    echo "Invalid unit value.";
                    exit();
                }
            }

            // Update the donation status (approved or rejected)
            $query = "UPDATE request SET status = :status WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":status", $input_status);
            $stmt->execute();

            // Redirect to the dashboard after the update
            header("Location: dashboard.php?requests_history=1");
            exit();
        }
    }

    // Fetch all pending requests (those that are not yet approved or rejected)
    $query = "SELECT patient.name, patient.username, patient.email, patient.blood, request.status, 
                     patient.latitude, patient.longitude, request.id, request.unit
              FROM request
              INNER JOIN patient ON request.patient_id = patient.id
              WHERE request.status = 'pending'"; // Only fetch pending requests
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo $e->getMessage();
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Manage Requests</h1>

        <?php if (isset($_GET['status'])): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-800">
                <?php echo $_GET['status'] === 'approved' ? 'Request approved successfully!' : 'Request rejected successfully!'; ?>
            </div>
        <?php endif; ?>

        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="p-2 border-b text-left">Patient Name</th>
                    <th class="p-2 border-b text-left">Username</th>
                    <th class="p-2 border-b text-left">Email</th>
                    <th class="p-2 border-b text-left">Blood Type</th>
                    <th class="p-2 border-b text-left">Status</th>
                    <th class="p-2 border-b text-left">Location</th> <!-- Location column -->
                    <th class="p-2 border-b text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donations as $request): ?>
                    <tr>
                        <td class="p-2 border-b"><?php echo htmlspecialchars($request['name']); ?></td>
                        <td class="p-2 border-b"><?php echo htmlspecialchars($request['username']); ?></td>
                        <td class="p-2 border-b"><?php echo htmlspecialchars($request['email']); ?></td>
                        <td class="p-2 border-b"><?php echo htmlspecialchars($request['blood']); ?></td>
                        <td class="p-2 border-b"><?php echo htmlspecialchars($request['status']); ?></td>
                        <td class="p-2 border-b">
                            <?php 
                                $latitude = $request['latitude'];
                                $longitude = $request['longitude'];
                                if ($latitude && $longitude) {
                                    echo "<a href='https://maps.google.com/?q=$latitude,$longitude' target='_blank'>View on Map</a>";
                                } else {
                                    echo "No location available";
                                }
                            ?>
                        </td>
                        <td class="p-2 border-b">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="patient_id" value="<?php echo $request['id']; ?>">
                                <button type="submit" name="approve" class="bg-green-500 text-white p-2 rounded">Approve</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="patient_id" value="<?php echo $request['id']; ?>">
                                <button type="submit" name="reject" class="bg-red-500 text-white p-2 rounded">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
