<?php
include 'db_connect.php';

// Manually include PHPMailer files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Detect if AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function sendPHPMailerEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'v.gugan16@gmail.com';
        $mail->Password   = 'vdlzmwgdrepbgqyt';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->Timeout    = 10;

        $mail->setFrom('v.gugan16@gmail.com', 'Sunface Support');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <h2 style="color: #007bff;">' . $subject . '</h2>
                <div style="white-space: pre-line; line-height: 1.6; padding: 15px; background: #f9f9f9; border-radius: 5px;">
                    ' . nl2br(htmlspecialchars($message)) . '
                </div>
                <p style="margin-top: 20px; font-size: 12px; color: #777;">
                    Please do not reply to this automated message.
                </p>
            </div>';
        $mail->AltBody = strip_tags($message);

        $mail->send();
        return ['success' => true];
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = $conn->real_escape_string($_POST['name']);
    $email     = $conn->real_escape_string($_POST['email']);
    $service   = intval($_POST['service_type']);
    $desc      = $conn->real_escape_string($_POST['description']);
    $ticket_no = strtoupper(uniqid('TKT'));
    $status    = 'Open';

    $res = $conn->query("SELECT name, team_email FROM service_types WHERE id = $service");
    if (!$res || $res->num_rows === 0) {
        $subject = "🚨 Invalid Service Type Attempt";
        $message = "Invalid service_type submitted.\n\nName: $name\nEmail: $email\nService ID: $service\nDescription: $desc\nTime: " . date("Y-m-d H:i:s");

        sendPHPMailerEmail("adeepakkumar26@gmail.com", $subject, $message);

        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Invalid service type selected. Our team has been notified.']);
        } else {
            echo "<script>alert('Invalid service type selected. Our team has been notified.'); window.location.href='raise_ticket.php';</script>";
        }
        exit;
    }

    $row = $res->fetch_assoc();
    $service_name = $row['name'];
    $team_email   = $row['team_email'];

    $stmt = $conn->prepare("INSERT INTO tickets (name, email, service_type, description, ticket_no, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $service_name, $desc, $ticket_no, $status);
    $stmt->execute();

    $ticket_result = $conn->query("SELECT * FROM tickets WHERE ticket_no = '$ticket_no'");
    $ticket_data = $ticket_result->fetch_assoc();

    if (!$ticket_data) {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Ticket creation failed.']);
        } else {
            echo "<script>alert('Ticket creation failed.'); window.location.href='raise_ticket.php';</script>";
        }
        exit;
    }

    // Emails
    $client_subject = "Ticket Raised: " . $ticket_data['ticket_no'];
    $client_message = "Dear " . $ticket_data['name'] . ",\n\nThank you for contacting Sunface Support.\n\nTicket Number: " . $ticket_data['ticket_no'] . "\nEmail: " . $ticket_data['email'] . "\nService: " . $ticket_data['service_type'] . "\nStatus: " . $ticket_data['status'] . "\n\nIssue:\n" . $ticket_data['description'] . "\n\nWe’ll get back to you shortly.\n\n- Sunface Support Team";

    $admin_subject = "New Support Ticket: " . $ticket_data['ticket_no'];
    $admin_message = "Ticket Number: " . $ticket_data['ticket_no'] . "\nName: " . $ticket_data['name'] . "\nEmail: " . $ticket_data['email'] . "\nService: " . $ticket_data['service_type'] . "\nStatus: " . $ticket_data['status'] . "\n\nDescription:\n" . $ticket_data['description'];

    $client_result = sendPHPMailerEmail($ticket_data['email'], $client_subject, $client_message);
    $admin_result  = sendPHPMailerEmail('pranaesh2004@gmail.com', $admin_subject, $admin_message);
    if (!empty($team_email)) {
        sendPHPMailerEmail($team_email, $admin_subject, $admin_message);
    }

    if ($client_result['success']) {
        $conn->query("UPDATE tickets SET auto_reply_sent = TRUE, auto_reply_time = NOW() WHERE ticket_no = '$ticket_no'");
    }

    if ($client_result['success'] && $admin_result['success']) {
        if ($isAjax) {
            echo json_encode([
                'success' => true,
                'message' => '✅ Ticket raised successfully! Your Ticket No: <strong>' . $ticket_no . '</strong>'
            ]);
        } else {
            echo "<script>alert('✅ Ticket raised successfully! Your Ticket No: $ticket_no'); window.location.href='raise_ticket.php';</script>";
        }
    } else {
        if ($isAjax) {
            echo json_encode([
                'success' => false,
                'message' => '⚠ Ticket #' . $ticket_no . ' was created, but email sending failed.'
            ]);
        } else {
            echo "<script>alert('⚠ Ticket #$ticket_no was created, but email sending failed.'); window.location.href='raise_ticket.php';</script>";
        }
    }
}
?>
