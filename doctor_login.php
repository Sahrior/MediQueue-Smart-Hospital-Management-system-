<?php
session_start();
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, full_name, password FROM doctors WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $doctor = $result->fetch_assoc();
        
        if (password_verify($password, $doctor['password'])) {
            $_SESSION['doctor_id'] = $doctor['id'];
            $_SESSION['doctor_name'] = $doctor['full_name'];
            
            header("Location: doctor_home.php");
            exit();
        } else {
            echo "<script>alert('Invalid password'); window.location.href='doctorlogin.html';</script>";
        }
    } else {
        echo "<script>alert('No account found with that email'); window.location.href='doctorlogin.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
