<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: studentlogin.html");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $ticket_id = intval($_GET['id']);
    $sql = "SELECT a.*, d.full_name as doctor_name, d.specialization 
            FROM appointments a
            JOIN doctors d ON a.doctor_id = d.id
            WHERE a.id = ? AND a.patient_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $ticket_id, $user_id);
} else {
    $sql = "SELECT a.*, d.full_name as doctor_name, d.specialization 
            FROM appointments a
            JOIN doctors d ON a.doctor_id = d.id
            WHERE a.patient_id = ? 
            ORDER BY a.id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();

if (!$ticket) {
    echo "Ticket not found or access denied. <a href='patientportal.php'>Go Home</a>";
    exit();
}

$sql_ahead = "SELECT COUNT(*) as count FROM appointments 
              WHERE doctor_id = ? AND status = 'waiting' AND id < ?";
$stmt2 = $conn->prepare($sql_ahead);
$stmt2->bind_param("ii", $ticket['doctor_id'], $ticket['id']);
$stmt2->execute();
$ahead_count = $stmt2->get_result()->fetch_assoc()['count'];

$wait_time = $ahead_count * 5;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Ticket</title>
    <link rel="stylesheet" href="patientticket.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body>

    <section class="full">
        <header class="header">
            <div class="header_left">
                <img src="logo.png" alt="">
                <h3>MediQueue</h3>
            </div>
            <div class="header_right">
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                <button onclick="window.location.href='patientportal.php'"><i class="ri-arrow-left-line"></i> Go Back</button>
            </div>
        </header>

        <div class="down">
            <div class="down_up">
                <p>Your Token Number</p>
                <h2>#<?php echo $ticket['ticket_number']; ?></h2>
            </div>

            <div class="down_down">
                <div class="down_down_up">
                    <div class="box">
                        <i class="ri-team-line"></i>
                        <h3><?php echo $ahead_count; ?></h3>
                        <p>People Ahead</p>
                    </div>

                    <div class="box">
                        <i class="ri-timer-line"></i>
                        <h3><?php echo $wait_time; ?> min</h3>
                        <p>Est. Wait Time</p>
                    </div>

                    <div class="box">
                        <i class="ri-check-double-fill"></i>
                        <h3><?php echo ucfirst($ticket['status']); ?></h3>
                        <p>Status</p>
                    </div>
                </div>

                <hr>

                <div class="down_down_down">
                    <div class="down_down_down_left">
                        <p>Department</p>
                        <h5><?php echo htmlspecialchars($ticket['specialization']); ?></h5>
                    </div>

                    <div class="down_down_down_right">
                        <p>Doctor</p>
                        <h5><?php echo htmlspecialchars($ticket['doctor_name']); ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
</body>
</html>