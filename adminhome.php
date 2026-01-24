<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminenter.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doctor_id']) && isset($_POST['current_status'])) {
    $doc_id = intval($_POST['doctor_id']);
    $current_status = $_POST['current_status'];
    
    $new_status = 'active';
    if ($current_status === 'active') {
        $new_status = 'break';
    } elseif ($current_status === 'break') {
        $new_status = 'offline';
    }
    
    $update_stmt = $conn->prepare("UPDATE doctors SET status = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_status, $doc_id);
    $update_stmt->execute();
    
    header("Location: adminhome.php");
    exit();
}

$patients_today_query = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE DATE(appointment_time) = CURDATE()");
$patients_today = $patients_today_query->fetch_assoc()['total'];

$active_doctors_query = $conn->query("SELECT COUNT(*) as total FROM doctors WHERE status = 'active'");
$active_doctors = $active_doctors_query->fetch_assoc()['total'];

$active_emergency_query = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE is_emergency = 1 AND status IN ('waiting', 'serving')");
$active_emergency = $active_emergency_query->fetch_assoc()['total'];


$search_term = "";
$sql_doctors = "SELECT d.id, d.full_name, d.specialization, d.status, 
                (SELECT COUNT(*) FROM appointments a WHERE a.doctor_id = d.id AND DATE(a.appointment_time) = CURDATE()) as patient_count
                FROM doctors d";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $conn->real_escape_string($_GET['search']);
    $sql_doctors .= " WHERE d.full_name LIKE '%$search_term%' OR d.specialization LIKE '%$search_term%'";
}

$result_doctors = $conn->query($sql_doctors);

$doctors_data = [];
$total_wait_sum = 0;
$doctor_count = 0;

if ($result_doctors->num_rows > 0) {
    while($row = $result_doctors->fetch_assoc()) {
        $calculated_wait = $row['patient_count'] * 5;
        $row['avg_wait_time'] = $calculated_wait;

        $total_wait_sum += $calculated_wait;
        $doctor_count++;

        $doctors_data[] = $row;
    }
}

$dashboard_avg_wait = ($doctor_count > 0) ? round($total_wait_sum / $doctor_count) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="adminhome.css">
    <style>
        .doctor-management .header form {
            display: flex;
            align-items: center;
        }
        .actions-btn {
            cursor: pointer;
            background: none;
            border: 1px solid #ddd;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .actions-btn:hover {
            background: #f0f0f0;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="left">
            <img src="logo.png" alt="">
            <h2>MediQueue</h2>
        </div>

        <div class="right">
            <p>Admin User</p>
            <a href="logout.php" class="admin_log_out" style="text-decoration:none; text-align:center;">
                <i class="ri-logout-box-r-line"></i> Logout
            </a>
        </div>
    </div>

    <div class="main">
        <div class="up">
            <h1>Admin Dashboard</h1>
            <p>Monitor hospital operations and manage staff</p>
        </div>

        <div class="middle">
            <div class="card">
                <i class="ri-user-line"></i>
                <h3><?php echo $patients_today; ?></h3>
                <p>Patients today</p>
            </div>

            <div class="card">
                <i class="ri-file-chart-line"></i>
                <h3><?php echo $active_doctors; ?></h3>
                <p>Active Doctors</p>
            </div>

            <div class="card">
                <i class="ri-time-line"></i>
                <h3><?php echo $dashboard_avg_wait; ?> min</h3>
                <p>Avg Wait Time</p>
            </div>

            <div class="cardA">
                <i class="ri-first-aid-kit-line"></i>
                <h3><?php echo $active_emergency; ?></h3>
                <p>Active emergency patient</p>
            </div>
        </div>

        <div class="down">
            <div class="doctor-management">
                
                <div class="header">
                    <h2>Doctor Management</h2>
                    <form action="" method="GET">
                        <input type="search" name="search" placeholder="Search doctors..." value="<?php echo htmlspecialchars($search_term); ?>" />
                    </form>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Patients Today</th>
                            <th>Avg Wait Time</th>
                            <th>Actions (Change Status)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($doctors_data) > 0) {
                            foreach($doctors_data as $row) {
                                $words = explode(" ", $row['full_name']);
                                $initials = "";
                                foreach ($words as $w) $initials .= strtoupper($w[0]);

                                $status_db = $row['status'];
                                $css_class = 'off';
                                if ($status_db == 'active') $css_class = 'active';
                                if ($status_db == 'break') $css_class = 'break';
                        ?>
                        <tr>
                            <td>
                                <span class="avatar"><?php echo $initials; ?></span>
                                <?php echo htmlspecialchars($row['full_name']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                            <td>
                                <span class="status <?php echo $css_class; ?>">
                                    <?php echo ucfirst($status_db); ?>
                                </span>
                            </td>
                            <td><?php echo $row['patient_count']; ?></td>
                            
                            <td><?php echo $row['avg_wait_time']; ?> min</td>
                            
                            <td>
                                <form action="" method="POST">
                                    <input type="hidden" name="doctor_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="current_status" value="<?php echo $status_db; ?>">
                                    <button type="submit" class="actions-btn" title="Cycle Status">
                                        ⋮
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center;'>No doctors found matching your search.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="script1.js"></script>
</body>
</html>