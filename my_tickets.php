<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: studentlogin.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT a.*, d.full_name as doctor_name, d.specialization 
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.patient_id = ? 
        ORDER BY a.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tickets</title>
    <link rel="stylesheet" href="patientportal.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet" />
    <style>
        /* Specific Styles for this page */
        .ticket-container {
            padding: 40px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .ticket-card {
            background: white;
            padding: 20px;
            border-radius: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.2s;
            border-left: 5px solid #ccc;
        }
        .ticket-card:hover { transform: translateY(-3px); }
        
        .ticket-card.waiting { border-left-color: #3b82f6; } /* Blue */
        .ticket-card.serving { border-left-color: #22c55e; } /* Green */
        .ticket-card.completed { border-left-color: #9ca3af; opacity: 0.8; } /* Grey */
        .ticket-card.cancelled { border-left-color: #ef4444; opacity: 0.7; } /* Red */

        .t-left h3 { margin-bottom: 5px; color: #1f2937; }
        .t-left p { color: #6b7280; font-size: 0.9rem; }
        .t-token {
            font-size: 1.5rem;
            font-weight: 700;
            color: #3b82f6;
            background: #eff6ff;
            padding: 10px 20px;
            border-radius: 12px;
        }
        .btn-view {
            padding: 10px 20px;
            background: #2563eb;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="header_left">
            <img src="logo.png" alt="">
            <h3>MediQueue</h3>
        </div>
        <div class="header_right">
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
            <a href="patientportal.php"><button style="background:#e5e7eb; color:black;">Back to Home</button></a>
        </div>
    </header>

    <div class="first1">
        <div class="first">
            <h1>My Appointments</h1>
            <p>Track your current status and history</p>
        </div>
    </div>

    <div class="ticket-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): 
                // Determine styling based on status
                $status = $row['status'];
                $class = $status; 
            ?>
                <div class="ticket-card <?php echo $class; ?>">
                    <div class="t-left">
                        <h3><?php echo htmlspecialchars($row['doctor_name']); ?></h3>
                        <p><?php echo htmlspecialchars($row['specialization']); ?></p>
                        <p style="margin-top:5px; font-size:0.8rem;">
                            <i class="ri-calendar-line"></i> <?php echo date("d M Y, h:i A", strtotime($row['appointment_time'])); ?>
                        </p>
                    </div>

                    <div class="t-center">
                        <span class="t-token">#<?php echo $row['ticket_number']; ?></span>
                    </div>

                    <div class="t-right" style="text-align:right;">
                        <div style="margin-bottom:22px; margin-right: 16px;">
                            <span class="status-badge" style="background:#eee;"><?php echo ucfirst($status); ?></span>
                        </div>
                        
                        <?php if($status == 'waiting' || $status == 'serving'): ?>
                            <a href="patientticket.php?id=<?php echo $row['id']; ?>" class="btn-view">Track Live</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; color:#6b7280;">You have no booked appointments.</p>
        <?php endif; ?>
    </div>

</body>
</html>