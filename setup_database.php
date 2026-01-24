<!-- 
How to Run It

Open your browser and go to: http://localhost/mediqueue/setup_database.php

You should see a "SETUP COMPLETE!" message.


-->


<?php
$host = "localhost";
$user = "root";
$pass = "";

// 1. Connect to MySQL (No Database selected yet)
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Create Database
$sql = "DROP DATABASE IF EXISTS mediqueue";
$conn->query($sql);

$sql = "CREATE DATABASE mediqueue";
if ($conn->query($sql) === TRUE) {
    echo "Database 'mediqueue' created successfully.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// 3. Select Database
$conn->select_db("mediqueue");

// 4. Create Tables

// --- Admins ---
$sql = "CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_code VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";
$conn->query($sql);

// --- Doctors ---
$sql = "CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    license_number VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    status ENUM('active', 'break', 'offline') NOT NULL DEFAULT 'offline',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// --- Users (Patients) ---
$sql = "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// --- Appointments ---
$sql = "CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_number INT NOT NULL,
    doctor_id INT NOT NULL,
    patient_id INT NOT NULL,
    appointment_time DATETIME NOT NULL,
    is_emergency BOOLEAN NOT NULL DEFAULT FALSE,
    status ENUM('waiting', 'serving', 'completed', 'cancelled') DEFAULT 'waiting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id),
    FOREIGN KEY (patient_id) REFERENCES users(id)
)";
$conn->query($sql);

echo "All tables created successfully.<br>";

// 5. Insert Dummy Data

// --- Password Hashes ---
$pass_123456 = password_hash("123456", PASSWORD_DEFAULT);
// Admin password is NOT hashed as per your previous logic (admin_login.php checks direct string)
$admin_pass = "admin";

// --- Insert Admin ---
$sql = "INSERT INTO admins (admin_code, password) VALUES ('admin', '$admin_pass')";
$conn->query($sql);
echo "Admin inserted (Code: admin / Pass: admin)<br>";

// --- Insert Doctors ---
$doctors = [
    ['Dr. Sarah Smith', 'sarah@test.com', 'LIC-001', 'Cardiology'],
    ['Dr. Michael Chen', 'michael@test.com', 'LIC-002', 'Neurology'],
    ['Dr. Emily Brown', 'emily@test.com', 'LIC-003', 'Orthopedics'],
    ['Dr. James Wilson', 'james@test.com', 'LIC-004', 'Pediatrics'],
    ['Dr. Lisa Park', 'lisa@test.com', 'LIC-005', 'General Medicine'],
    ['Dr. Robert Taylor', 'robert@test.com', 'LIC-006', 'Dermatology']
];

foreach ($doctors as $doc) {
    // Randomize status for variety
    $statuses = ['active', 'break', 'offline'];
    $rand_status = $statuses[array_rand($statuses)];

    $stmt = $conn->prepare("INSERT INTO doctors (full_name, email, license_number, password, specialization, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $doc[0], $doc[1], $doc[2], $pass_123456, $doc[3], $rand_status);
    $stmt->execute();
}
echo "6 Doctors inserted (Pass: 123456)<br>";

// --- Insert Patients ---
$patients = [
    ['Alice Johnson', 'alice@test.com'],
    ['Bob Williams', 'bob@test.com'],
    ['Charlie Davis', 'charlie@test.com'],
    ['Diana Evans', 'diana@test.com'],
    ['Ethan Hall', 'ethan@test.com']
];

foreach ($patients as $p) {
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $p[0], $p[1], $pass_123456);
    $stmt->execute();
}
echo "5 Patients inserted (Pass: 123456)<br>";

// --- Insert Appointments (For Jan 25, 2026) ---
// We will generate 15 random appointments
for ($i = 1; $i <= 15; $i++) {
    $ticket_num = $i;
    $doc_id = rand(1, 6); // Random doctor 1-6
    $pat_id = rand(1, 5); // Random patient 1-5

    // Time between 09:00 and 17:00
    $hour = rand(9, 16);
    $min = rand(0, 59);
    $time_str = sprintf("2026-01-25 %02d:%02d:00", $hour, $min);

    // 20% chance of emergency
    $is_emerg = (rand(1, 100) <= 20) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO appointments (ticket_number, doctor_id, patient_id, appointment_time, is_emergency, status) VALUES (?, ?, ?, ?, ?, 'waiting')");
    $stmt->bind_param("iiisi", $ticket_num, $doc_id, $pat_id, $time_str, $is_emerg);
    $stmt->execute();
}
echo "15 Appointments inserted for Jan 25, 2026.<br>";

$conn->close();
echo "<strong>SETUP COMPLETE!</strong>";
?>