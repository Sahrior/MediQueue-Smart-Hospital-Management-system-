<?php
session_start();
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_code = $_POST['admin_code'];
    $password = $_POST['password'];

    $sql = "SELECT id, admin_code, password FROM admins WHERE admin_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        // Direct password comparison (as requested: not hashed)
        if ($password === $admin['password']) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_code'] = $admin['admin_code'];
            
            // Redirect to the new PHP page
            header("Location: adminhome.php");
            exit();
        } else {
            echo "<script>alert('Invalid password'); window.location.href='adminenter.html';</script>";
        }
    } else {
        echo "<script>alert('Invalid Admin Code'); window.location.href='adminenter.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>