<?php
    session_start();
    include 'db_config.php';

    if (!isset($_SESSION['doctor_name'])) {
        header("Location: doctorlogin.html");
        exit();
    }

    $doctor_id = $_SESSION['doctor_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="doctorhome.css">
</head>
<body>

    <div class="navbar">
        <div class="left">
            <img src="logo.png" alt="MediQueue Logo">
            <h2>MediQueue</h2>
        </div>
        <div class="right">
            <p>Dr. <?php echo htmlspecialchars($_SESSION['doctor_name']); ?></p>
            <button class="doctor_log_out" id="doctor_logout" onclick="window.location.href='logout.php'"> 
                <i class="ri-logout-box-r-line"></i> Logout
            </button>
        </div>
    </div>

    <div class="main">
        <div class="top">
            <div class="card current-patient">
                <div class="card-header">
                    <span class="icon">👤</span>
                    <h4>Current Patient</h4>
                </div>
                <div class="card-body">
                    <?php
                    $sql_curr = "SELECT a.ticket_number, u.full_name 
                                 FROM appointments a 
                                 JOIN users u ON a.patient_id = u.id 
                                 WHERE a.doctor_id = ? AND a.status = 'serving' 
                                 LIMIT 1";
                    
                    $stmt_curr = $conn->prepare($sql_curr);
                    $stmt_curr->bind_param("i", $doctor_id);
                    $stmt_curr->execute();
                    $res_curr = $stmt_curr->get_result();

                    if ($row = $res_curr->fetch_assoc()) {
                        echo '<div class="serving-info">';
                        echo '<h1 style="font-size: 56px; color: #2b7a78; margin: 10px 0;">#' . $row['ticket_number'] . '</h1>';
                        echo '<p class="title" style="font-size: 18px; font-weight: 600;">' . htmlspecialchars($row['full_name']) . '</p>';
                        echo '<p class="subtitle">Currently being served</p>';
                        echo '</div>';
                    } else {
                        echo '<div class="status-icon">🕒</div>';
                        echo '<p class="title">No patient currently being served</p>';
                        echo '<p class="subtitle">Click "Call Next" to start</p>';
                    }
                    $stmt_curr->close();
                    ?>
                </div>
            </div>

            <div class="card upcoming-patients">
                <div class="card-header">
                    <span class="icon">👥</span>
                    <h4>Upcoming Patients</h4>
                </div>
                <div class="card-body">
                    <?php
                    $sql_q = "SELECT a.ticket_number, u.full_name, a.is_emergency 
                              FROM appointments a 
                              JOIN users u ON a.patient_id = u.id 
                              WHERE a.doctor_id = ? AND a.status = 'waiting' 
                              ORDER BY a.is_emergency DESC, a.appointment_time ASC";

                    $stmt_q = $conn->prepare($sql_q);
                    $stmt_q->bind_param("i", $doctor_id);
                    $stmt_q->execute();
                    $res_q = $stmt_q->get_result();

                    if ($res_q->num_rows > 0) {
                        echo '<ul style="list-style: none; padding: 0;">';
                        while ($row = $res_q->fetch_assoc()) {
                            $isEmerg = $row['is_emergency'] == 1;
                            $style = $isEmerg ? 'background-color: #fee2e2; border-left: 4px solid #dc2626;' : '';
                            $label = $isEmerg ? '<span style="color: #dc2626; font-weight: bold;">[EMERGENCY]</span> ' : '';

                            echo '<li style="padding: 12px; border-bottom: 1px solid #eee; margin-bottom: 5px; border-radius: 4px; ' . $style . '">';
                            echo '<strong>#' . $row['ticket_number'] . '</strong> - ' . $label . htmlspecialchars($row['full_name']);
                            echo '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<div style="text-align: center; padding: 20px;">';
                        echo '<div class="status-icon" style="font-size: 26px;">👤</div>';
                        echo '<p class="subtitle">Queue is empty</p>';
                        echo '</div>';
                    }
                    $stmt_q->close();
                    ?>
                </div>
            </div>

            <form action="update_queue.php" method="POST" class="actions">
                <button type="submit" name="action" value="call_next" class="btn call-next">
                    📞 Call Next
                </button>
                <button type="submit" name="action" value="skip" class="btn skip">
                    ⏭ Skip / Absent
                </button>
            </form>

            <div class="stats">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <?php
                        $q_count = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE doctor_id = $doctor_id AND status = 'waiting'")->fetch_assoc();
                        echo "<h3>" . ($q_count['total'] ?? 0) . "</h3>";
                        ?>
                        <p>In Queue</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon danger">⚠️</div>
                    <div class="stat-info">
                        <?php
                        $e_count = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE doctor_id = $doctor_id AND status = 'waiting' AND is_emergency = 1")->fetch_assoc();
                        echo "<h3>" . ($e_count['total'] ?? 0) . "</h3>";
                        ?>
                        <p>Emergencies</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="script1.js"></script>
</body>
</html>