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
        // Check if the 'donation_id' exists in the POST request
        if (isset($_POST["donation_id"]) && (isset($_POST["approve"]) || isset($_POST["reject"]))) {
            $id = $_POST['donation_id'];
            $input_status = isset($_POST['approve']) ? "approved" : "rejected";

            // Fetch the donation details
            $query = "SELECT * FROM donate WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if the result is valid
            if ($result === false) {
                echo "Donation not found.";
                exit();
            }

            if ($input_status == "approved") {
                // Handle blood unit update for approved donation
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
                    // Update the blood quantity in the blood table
                    $blood_id = 1; // Assuming 1 is the correct ID for the blood record
                    $query = "UPDATE blood SET {$blood} = {$blood} + :unit WHERE id = :id";
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
            $query = "UPDATE donate SET status = :status WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":status", $input_status);
            $stmt->execute();

            // Redirect to the dashboard after the update
            header("Location: dashboard.php?donations_history=1");
            exit();
        }
    }

    // Fetch all pending donations (those that are not yet approved or rejected)
    $query = "SELECT donor.name, donor.username, donor.email, donor.blood, donate.status, 
                     donor.latitude, donor.longitude, donate.id, donate.unit
              FROM donate
              INNER JOIN donor ON donate.donor_id = donor.id
              WHERE donate.status = 'pending'"; // Only fetch pending donations
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
    <title>Manage Donations</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Manage Donations</h1>

        <?php if (isset($_GET['status'])): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-800">
                <?php echo $_GET['status'] === 'approved' ? 'Donation approved successfully!' : 'Donation rejected successfully!'; ?>
            </div>
        <?php endif; ?>

        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="p-2 border-b text-left">Donor Name</th>
                    <th class="p-2 border-b text-left">Username</th>
                    <th class="p-2 border-b text-left">Email</th>
                    <th class="p-2 border-b text-left">Blood Type</th>
                    <th class="p-2 border-b text-left">Status</th>
                    <th class="p-2 border-b text-left">Location</th> <!-- Location column -->
                    <th class="p-2 border-b text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donations as $donation): ?>
                    <tr>
                        <td class="p-2 border-b"><?php echo htmlspecialchars($donation['name']); ?></td>
                        <td class="p-2 border-b"><?php echo htmlspecialchars($donation['username']); ?></td>
                        <td class="p-2 border-b"><?php echo htmlspecialchars($donation['email']); ?></td>
                        <td class="p-2 border-b"><?php echo htmlspecialchars($donation['blood']); ?></td>
                        <td class="p-2 border-b"><?php echo htmlspecialchars($donation['status']); ?></td>
                        <td class="p-2 border-b">
                            <?php 
                                $latitude = $donation['latitude'];
                                $longitude = $donation['longitude'];
                                if ($latitude && $longitude) {
                                    echo "<a href='https://maps.google.com/?q=$latitude,$longitude' target='_blank'>View on Map</a>";
                                } else {
                                    echo "No location available";
                                }
                            ?>
                        </td>
                        <td class="p-2 border-b">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="donation_id" value="<?php echo $donation['id']; ?>">
                                <button type="submit" name="approve" class="bg-green-500 text-white p-2 rounded">Approve</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="donation_id" value="<?php echo $donation['id']; ?>">
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
