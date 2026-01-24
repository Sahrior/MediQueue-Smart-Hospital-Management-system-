<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $license_number = $_POST['license_number'];
    $specialization = $_POST['specialization'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);


    $sql = "INSERT INTO doctors (full_name, email, license_number, password, specialization) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $full_name, $email, $license_number, $password, $specialization);
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='doctorlogin.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Registration</title>
    <link rel="stylesheet" href="doctorsignup.css">
</head>

<body>

    <form method="post">
        <section class="one">

            <div class="left">

                <div class="left_top">
                    <img src="logo.png" alt="">
                    <h3>MediQueue</h3>
                </div>

                <div class="left_middle">
                    <h1>
                        Serve Smarter, <br>Not Harder.
                    </h1>

                    <p>
                        Manage appointments efficiently and focus <br>
                        on what matters most — patient care.
                    </p>
                </div>

                <div class="left_bottom">

                    <div class="card1">
                        <h2>300+</h2>
                        <p>Registered Doctors</p>
                    </div>

                    <div class="card2">
                        <h2>95%</h2>
                        <p>On-time Consults</p>
                    </div>

                    <div class="card3">
                        <h2>24/7</h2>
                        <p>Queue Access</p>
                    </div>

                </div>

            </div>

            <div class="right">

                <div class="main_card">

                    <div class="change">
                        <button id="doctor_login_btn">Login</button>
                        <button id="doctor_registration_btn">Registration</button>
                    </div>

                    <div class="box">

                        <h1>Doctor Registration</h1>
                        <p>Create your professional account</p>

                        <div class="name">
                            <label>Full Name:</label>
                            <input type="text" name="full_name" placeholder="Dr. John Doe" required>
                        </div>

                        <div class="email">
                            <label>Email:</label>
                            <input type="email" name="email" placeholder="Must contain @" required>
                        </div>

                        <div class="specialization">
                            <label>Specialization:</label>
                            <input type="text" name="specialization" placeholder="Cardiology" required>
                        </div>

                        <div class="password">
                            <label>Password:</label>
                            <input type="password" name="password" required>
                        </div>

                    </div>

                    <div class="student_sign_Up_btn">
                        <button>Sign UP</button>
                    </div>

                    <br>
                    <br>

                    <hr>

                    <div class="others">

                        <div class="up">
                            <p>Others Portal</p>
                        </div>

                        <div class="down">
                            <a href="">Student</a>
                            <a href="">Admin</a>
                            <a href="public_display_system.html">Public display</a>
                        </div>

                    </div>

                </div>

            </div>

        </section>
    </form>

    <script src="script1.js"></script>

</body>

</html>