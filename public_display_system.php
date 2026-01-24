<?php
include 'db_config.php';

// --- MODE B: The "Monitor" (Specific Doctor Selected) ---
if (isset($_GET['doctor_id'])) {
    $doctor_id = intval($_GET['doctor_id']);

    // Query 1: "Who is the Doctor?"
    $stmt = $conn->prepare("SELECT full_name, specialization FROM doctors WHERE id = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $doctor = $stmt->get_result()->fetch_assoc();

    if (!$doctor) {
        die("Doctor not found. <a href='public_display_system.php'>Go Back</a>");
    }

    // Query 2: "Who is Inside?" (Now Calling)
    // We select the ONE patient currently being served
    $sql_current = "SELECT ticket_number, is_emergency FROM appointments 
                    WHERE doctor_id = ? AND status = 'serving' LIMIT 1";
    $stmt_curr = $conn->prepare($sql_current);
    $stmt_curr->bind_param("i", $doctor_id);
    $stmt_curr->execute();
    $current_patient = $stmt_curr->get_result()->fetch_assoc();

    // Query 3: "Who is Next?" (Upcoming)
    // Sort by Emergency (DESC) then Ticket Number (ASC)
    $sql_queue = "SELECT ticket_number, is_emergency FROM appointments 
                  WHERE doctor_id = ? AND status = 'waiting' 
                  ORDER BY is_emergency DESC, ticket_number ASC";
    $stmt_queue = $conn->prepare($sql_queue);
    $stmt_queue->bind_param("i", $doctor_id);
    $stmt_queue->execute();
    $queue_result = $stmt_queue->get_result();
}

// --- MODE A: The "Lobby" (No Doctor Selected) ---
else {
    // Fetch all active doctors to display selection menu
    $doctors_list = $conn->query("SELECT id, full_name, specialization, status FROM doctors");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediQueue Display</title>

    <?php if (isset($doctor_id)): ?>
        <meta http-equiv="refresh" content="5">
    <?php endif; ?>

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />
<link rel="stylesheet" href="public_display_system.css?v=<?php echo time(); ?>">
</head>

<body>

    <section class="one">
        <div class="one_one">
            <?php if (isset($doctor_id)): ?>
                <a href="public_display_system.php" id="backBtn">Change Room</a>
            <?php else: ?>
                <a href="index.html" id="backBtn">Home</a>
            <?php endif; ?>

            <div class="one_one_one">
                <h3>MediQueue</h3>
                <p>Public Display System</p>
            </div>
        </div>

        <div class="one_two">
            <h5 id="clock">00:00:00</h5>
            <h6 id="date">Loading...</h6>
        </div>
    </section>

    <?php if (isset($doctor_id)): ?>
        <section class="two monitor-mode">

            <div class="left <?php echo ($current_patient && $current_patient['is_emergency']) ? 'emergency-pulse' : ''; ?>">
                <div class="room-info">
                    <h2><?php echo htmlspecialchars($doctor['specialization']); ?></h2>
                    <h3><?php echo htmlspecialchars($doctor['full_name']); ?></h3>
                </div>

                <div class="center">
                    <?php if ($current_patient): ?>
                        <p class="status-label">
                            <i class="ri-volume-up-line"></i> NOW CALLING
                        </p>

                        <?php if ($current_patient['is_emergency']): ?>
                            <div class="emergency-badge">EMERGENCY</div>
                        <?php endif; ?>

                        <h1 class="big-token">
                            <i class="ri-hashtag"></i><?php echo $current_patient['ticket_number']; ?>
                        </h1>
                        <p class="instruction">Please proceed to room</p>
                    <?php else: ?>
                        <i class="ri-moon-line" style="font-size: 4rem; opacity: 0.5;"></i>
                        <h2 style="margin-top: 20px;">Waiting for Doctor...</h2>
                    <?php endif; ?>
                </div>
            </div>

            <div class="right">
                <div class="queue-header">
                    <h2><i class="ri-time-line"></i> Upcoming</h2>
                    <span class="count"><?php echo $queue_result->num_rows; ?> in line</span>
                </div>

                <div class="queue-list">
                    <?php
                    if ($queue_result->num_rows > 0) {
                        while ($row = $queue_result->fetch_assoc()) {
                            $is_emerg = $row['is_emergency'] == 1 ? 'emergency-row' : '';
                            echo '<div class="box_1 ' . $is_emerg . '">';
                            echo '<h4><i class="ri-hashtag"></i>' . $row['ticket_number'] . '</h4>';
                            if ($row['is_emergency']) {
                                echo '<span class="emerg-tag">Urgent</span>';
                            } else {
                                echo '<span class="wait-tag">Waiting</span>';
                            }
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="empty-state">Queue is empty</div>';
                    }
                    ?>
                </div>
            </div>

        </section>

    <?php else: ?>
        <section class="lobby-mode">
            <h1>Select Display Screen</h1>
            <p>Choose the doctor assigned to this waiting area TV</p>

            <div class="doctor-grid">
                <?php while ($doc = $doctors_list->fetch_assoc()): ?>
                    <a href="?doctor_id=<?php echo $doc['id']; ?>" class="doctor-card <?php echo $doc['status']; ?>">
                        <div class="icon-box">
                            <i class="ri-stethoscope-line"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($doc['full_name']); ?></h3>
                        <p><?php echo htmlspecialchars($doc['specialization']); ?></p>
                        <span class="status-dot"><?php echo ucfirst($doc['status']); ?></span>
                    </a>
                <?php endwhile; ?>
            </div>
        </section>
    <?php endif; ?>

    <section class="three">
        <div class="three_one">
            <img src="logo.png" alt="">
            <h2>MediQueue</h2>
        </div>
        <p>© <?php echo date("Y"); ?> MediQueue System. Automated Display.</p>
    </section>

    <script>
        // Simple JS Clock for the Header
        function updateTime() {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString();
            document.getElementById('date').innerText = now.toDateString();
        }
        setInterval(updateTime, 1000);
        updateTime();
    </script>
</body>

</html>