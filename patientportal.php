<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: studentlogin.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$dept_sql = "SELECT DISTINCT specialization FROM doctors WHERE specialization IS NOT NULL AND specialization != ''";
$dept_result = $conn->query($dept_sql);

$selected_dept = isset($_GET['dept']) ? $_GET['dept'] : '';
$doc_sql = "SELECT * FROM doctors";
if ($selected_dept) {
    $doc_sql .= " WHERE specialization = '" . $conn->real_escape_string($selected_dept) . "'";
}
$doc_result = $conn->query($doc_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal</title>
    <link rel="stylesheet" href="patientportal.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet" />
</head>

<body>

    <div id="appointmentModal" class="modal-overlay">
        <div class="modal-content">
            <h3>📅 Book Standard Appointment</h3>
            <form action="book_appointment.php" method="POST">
                <input type="hidden" name="type" value="standard">
                <input type="hidden" name="doctor_id" id="modal_doctor_id">

                <label>Select Date:</label>
                <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>">

                <label>Select Time:</label>
                <input type="time" name="time" required>

                <div class="modal-buttons">
                    <button type="button" onclick="closeModal()" class="cancel-btn">Cancel</button>
                    <button type="submit" class="confirm-btn">Confirm Booking</button>
                </div>
            </form>
        </div>
    </div>

    <section class="full">
        <header class="header">
            <div class="header_left">
                <img src="logo.png" alt="">
                <h3>MediQueue</h3>
            </div>
            <div class="header_right">
                <p>Welcome, <?php echo htmlspecialchars($user_name); ?></p>

                <a href="my_tickets.php" style="text-decoration:none;">
                    <button class="btn-tickets" style="background: #3b82f6; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                        <i class="ri-ticket-2-line"></i> My Tickets
                    </button>
                </a>

                <a href="logout.php"><button id="logout_patient"><i class="ri-picture-in-picture-exit-line"></i> Logout</button></a>
            </div>
        </header>

        <div class="first1">
            <div class="first">
                <h1>Book an Appointment</h1>
                <p>Select a department to filter doctors</p>
            </div>
        </div>

        <div class="text">
            <h4>Select Department</h4>
        </div>
        <div class="second">
            <a href="patientportal.php" style="text-decoration:none;">
                <div class="box">
                    <div class="sign"><i class="ri-hospital-line"></i></div>
                    <p style="color:black;">All Departments</p>
                </div>
            </a>

            <?php while ($dept = $dept_result->fetch_assoc()): ?>
                <a href="?dept=<?php echo urlencode($dept['specialization']); ?>" style="text-decoration:none;">
                    <div class="box">
                        <div class="sign"><i class="ri-service-line"></i></div>
                        <p style="color:black;"><?php echo htmlspecialchars($dept['specialization']); ?></p>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>

        <div class="text">
            <h4>Available Doctors <?php echo $selected_dept ? "in " . htmlspecialchars($selected_dept) : ""; ?></h4>
        </div>

        <div class="last">
            <?php
            if ($doc_result->num_rows > 0):
                while ($doc = $doc_result->fetch_assoc()):
                    $status_color = ($doc['status'] == 'active') ? '#4ade80' : (($doc['status'] == 'break') ? '#facc15' : '#9ca3af');
            ?>
                    <div class="dbox">
                        <div class="up">
                            <div class="up_left">
                                <img src="doctor.png" alt="">
                            </div>
                            <div class="up_right">
                                <h5><?php echo htmlspecialchars($doc['full_name']); ?></h5>
                                <p><?php echo htmlspecialchars($doc['specialization']); ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="down">
                            <div class="down_up">
                                <div class="availibilty_status" style="background-color: <?php echo $status_color; ?>;">
                                    <?php echo ucfirst($doc['status']); ?>
                                </div>
                            </div>

                            <div class="down_right">
                                <div class="standard_appointment">
                                    <button type="button" class="btn-standard" onclick="openModal(<?php echo $doc['id']; ?>)">
                                        Standard <br>Appointment
                                    </button>
                                </div>

                                <div class="emergency_appointment">
                                    <form action="book_appointment.php" method="POST">
                                        <input type="hidden" name="type" value="emergency">
                                        <input type="hidden" name="doctor_id" value="<?php echo $doc['id']; ?>">
                                        <button type="submit" class="btn-emergency" onclick="return confirm('Are you sure this is an EMERGENCY? This will be booked immediately.')">
                                            Emergency
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                endwhile;
            else:
                ?>
                <p style="padding: 20px;">No doctors found in this department.</p>
            <?php endif; ?>
        </div>
    </section>

    <script>
        function openModal(doctorId) {
            document.getElementById('modal_doctor_id').value = doctorId;
            document.getElementById('appointmentModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('appointmentModal').style.display = 'none';
        }
    </script>

</body>

</html>