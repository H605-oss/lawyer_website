<?php
session_start();
// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Include the database connection file
include '../includes/db.php';

// Initialize variables
$appointment = null;
$error_message = '';
$success_message = '';

// Check if an appointment ID was provided in the URL
if (isset($_GET['id'])) {
    $appointmentId = $_GET['id'];
    
    // Fetch the appointment data from the database
    $sql = "SELECT * FROM appointments WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("i", $appointmentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $appointment = $result->fetch_assoc();
        } else {
            $error_message = "Appointment not found.";
        }
        $stmt->close();
    } else {
        $error_message = "Error preparing the statement: " . $conn->error;
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the form submission for updating the appointment
    $appointmentId = trim($_POST['id']);
    $fullName = trim($_POST['fullName']);
    $emailAddress = trim($_POST['emailAddress']);
    $phoneNumber = trim($_POST['phoneNumber']);
    $appointmentDate = trim($_POST['appointmentDate']);
    $appointmentTime = trim($_POST['appointmentTime']);
    $preferredAttorney = trim($_POST['preferredAttorney']);
    $legalIssue = trim($_POST['legalIssue']);
    $legalMatter = trim($_POST['legalMatter']);

    // Check if the 'Other' option was selected for legal matter
    if ($legalMatter === 'Other' && isset($_POST['otherLegalMatter'])) {
        $legalMatter = trim($_POST['otherLegalMatter']);
    }

    // Prepare SQL UPDATE statement
    $sql = "UPDATE appointments SET full_name=?, email_address=?, phone_number=?, appointment_date=?, appointment_time=?, preferred_attorney=?, legal_issue=?, legal_matter=? WHERE id=?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssssssi", $fullName, $emailAddress, $phoneNumber, $appointmentDate, $appointmentTime, $preferredAttorney, $legalIssue, $legalMatter, $appointmentId);
        if ($stmt->execute()) {
            $success_message = "Appointment updated successfully.";
            // Redirect to the appointments list after update
            header("Location: manage_appointments.php?status=updated");
            exit();
        } else {
            $error_message = "Error updating appointment: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Error preparing statement: " . $conn->error;
    }
} else {
    $error_message = "No appointment ID provided.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --bg-color: #f4f4f4;
            --card-bg-color: #ffffff;
            --text-color: #333333;
            --header-bg-color: #2c3e50;
            --sidebar-color: #2c3e50;
            --primary-color: #3498db;
            --accent-color: #20c997;
            --delete-color: #e74c3c;
            --edit-color: #3498db;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            background-color: var(--bg-color);
            display: flex;
            height: 100vh;
            color: var(--text-color);
        }
        
        .container {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
            max-width: 800px;
            margin: auto;
        }

        .edit-form-container {
            background-color: var(--card-bg-color);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .edit-form-container h1 {
            color: var(--header-bg-color);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], input[type="tel"], input[type="date"], input[type="time"], textarea, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .btn-container {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: #fff;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }

        .message-container {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="edit-form-container">
            <h1>Edit Appointment</h1>
            
            <?php
            if ($success_message) {
                echo '<div class="message-container success">' . htmlspecialchars($success_message) . '</div>';
            } elseif ($error_message) {
                echo '<div class="message-container error">' . htmlspecialchars($error_message) . '</div>';
            }
            
            if ($appointment) {
            ?>
            <form action="edit_appointment.php" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($appointment['id']); ?>">
                <div class="form-group">
                    <label for="fullName">Full Name:</label>
                    <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($appointment['full_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="emailAddress">Email Address:</label>
                    <input type="email" id="emailAddress" name="emailAddress" value="<?php echo htmlspecialchars($appointment['email_address']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phoneNumber">Phone Number:</label>
                    <input type="tel" id="phoneNumber" name="phoneNumber" value="<?php echo htmlspecialchars($appointment['phone_number']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="appointmentDate">Appointment Date:</label>
                    <input type="date" id="appointmentDate" name="appointmentDate" value="<?php echo htmlspecialchars($appointment['appointment_date']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="appointmentTime">Appointment Time:</label>
                    <input type="time" id="appointmentTime" name="appointmentTime" value="<?php echo htmlspecialchars($appointment['appointment_time']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="preferredAttorney">Preferred Attorney:</label>
                    <input type="text" id="preferredAttorney" name="preferredAttorney" value="<?php echo htmlspecialchars($appointment['preferred_attorney']); ?>">
                </div>
                <div class="form-group">
                    <label for="legalIssue">Legal Issue:</label>
                    <textarea id="legalIssue" name="legalIssue" required><?php echo htmlspecialchars($appointment['legal_issue']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="legalMatter">Legal Matter:</label>
                    <input type="text" id="legalMatter" name="legalMatter" value="<?php echo htmlspecialchars($appointment['legal_matter']); ?>" required>
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="manage_appointments.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
            <?php
            }
            ?>
        </div>
    </div>
</body>
</html>
