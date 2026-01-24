<?php
    session_start();
    include 'db_config.php';

    if (!isset($_SESSION['doctor_id']) || !isset($_POST['action'])) {
        header("Location: doctor_home.php");
        exit();
    }

    $doctor_id = $_SESSION['doctor_id'];
    $action = $_POST['action'];

    if ($action == 'call_next') {
        $conn->query("UPDATE appointments SET status = 'completed' 
                    WHERE doctor_id = $doctor_id AND status = 'serving'");
    }

    $sql = "SELECT id FROM appointments 
            WHERE doctor_id = ? AND status = 'waiting' 
            ORDER BY is_emergency DESC, appointment_time ASC 
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $next_patient = $result->fetch_assoc();
        $appointment_id = $next_patient['id'];

        $new_status = ($action == 'call_next') ? 'serving' : 'cancelled';

        $update_sql = "UPDATE appointments SET status = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $new_status, $appointment_id);
        $update_stmt->execute();
    }

    header("Location: doctor_home.php");
    exit();
?>