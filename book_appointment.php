<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: patientportal.php");
    exit();
}

$patient_id = $_SESSION['user_id'];
$doctor_id = intval($_POST['doctor_id']);
$type = $_POST['type'];

if ($type == 'emergency') {
    $appointment_time = date('Y-m-d H:i:s');
    $is_emergency = 1;
} else {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $appointment_time = "$date $time:00";
    $is_emergency = 0;
}

$ticket_sql = "SELECT MAX(ticket_number) as max_ticket FROM appointments WHERE DATE(appointment_time) = CURDATE()";
$ticket_res = $conn->query($ticket_sql);
$ticket_row = $ticket_res->fetch_assoc();
$new_ticket = ($ticket_row['max_ticket']) ? $ticket_row['max_ticket'] + 1 : 1;

$stmt = $conn->prepare("INSERT INTO appointments (ticket_number, doctor_id, patient_id, appointment_time, is_emergency, status) VALUES (?, ?, ?, ?, ?, 'waiting')");
$stmt->bind_param("iiisi", $new_ticket, $doctor_id, $patient_id, $appointment_time, $is_emergency);

if ($stmt->execute()) {
    $_SESSION['last_ticket'] = $new_ticket; 
    header("Location: patientticket.php"); 
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>