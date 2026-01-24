<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>alert('This email is already registered. Please login.'); window.location.href='studentlogin.html';</script>";
    } else {
        $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $full_name, $email, $password);

        if ($stmt->execute()) {
            echo "<script>alert('Registration Successful! Please Login.'); window.location.href='studentlogin.html';</script>";
        } else {
            echo "<script>alert('Error: Could not register. Try again.'); window.history.back();</script>";
        }
        $stmt->close();
    }

    $check_stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="studentregistration.css">
</head>

<body>

    <form action="paitent_register.php" method="POST">

        <section class="one">
            <div class="left">

                <div class="left_top">
                    <img src="logo.png" alt="">
                    <h3>MediQueue</h3>
                </div>


                <div class="left_middle">
                    <h1>
                        Skip the Wait, <br>Not the Care.
                    </h1>

                    <p>
                        Our smart queue management system ensures you <br>
                        spend less time waiting and more time with your doctor.
                    </p>
                </div>


                <div class="left_bottom">
                    <div class="card1">
                        <h2>500+</h2>
                        <P>Daily Patients</P>
                    </div>

                    <div class="card2">
                        <h2>15 min</h2>
                        <p>Avg. wait time</p>
                    </div>

                    <div class="card3">
                        <h2>100%</h2>
                        <p>Patient Satisfaction</p>
                    </div>

                </div>

            </div>


            <div class="right">

                <div class="main_card">

                    <div class="change">
                        <Button id="student_login_btn">Login</Button>
                        <button id="student_registration_btn">Registration</button>
                    </div>

                    <div class="box">

                        <h1>Welcome Back</h1>
                        <p>Enter your credentials to access your account</p>

                        <div class="name">

                            <label>Name: </label>
                            <input type="text" name="name" id="" placeholder="kim tan" required>

                        </div>

                        <div class="email">

                            <label>Email: </label>
                            <input type="email" name="email" id="" placeholder="Must contailn @" required>

                        </div>


                        <div class="password">

                            <label>Password:</label>
                            <input type="password" name="password" id="" placeholder="password" required>

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
                            <P>Others Portal</P>
                        </div>

                        <div class="down">
                            <a href="">Doctor</a>
                            <a href="">Admin</a>
                            <a href="public_display_system.html">Public dispaly</a>
                        </div>

                    </div>
                </div>
            </div>
        </section>

    </form>

    <script src="script1.js"></script>

</body>

</html>