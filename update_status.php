<?php
include 'db_connect.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = $conn->real_escape_string($_POST['ticket_id']);
    $new_status = $conn->real_escape_string($_POST['status']);
    $resolution = $conn->real_escape_string($_POST['resolution']);

    // Get current ticket data
    $ticket_query = $conn->query("SELECT * FROM tickets WHERE id = '$ticket_id'");
    if (!$ticket_query || $ticket_query->num_rows === 0) {
        die("<script>alert('Ticket not found'); window.history.back();</script>");
    }

    $ticket = $ticket_query->fetch_assoc();

    // Update ticket status
    $update_query = $conn->query("UPDATE tickets SET status = '$new_status', resolution = '$resolution', updated_at = NOW() WHERE id = '$ticket_id'");

    if ($update_query) {
        // Send email notification to client
        sendStatusUpdateEmail($ticket, $new_status, $resolution);
        
        echo "<script>
            alert('Ticket status updated successfully!');
            window.location.href = 'admin_panel.php';
        </script>";
    } else {
        echo "<script>
            alert('Error updating ticket status');
            window.history.back();
        </script>";
    }
}

function sendStatusUpdateEmail($ticket, $new_status, $resolution) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings (Gmail SMTP)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'v.gugan16@gmail.com';
        $mail->Password   = 'vdlzmwgdrepbgqyt';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('v.gugan16@gmail.com', 'Sunface Support');
        $mail->addAddress($ticket['email']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Ticket #{$ticket['ticket_no']} Status Update: $new_status";
        
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #007bff;'>Ticket Status Update: {$ticket['ticket_no']}</h2>
                <div style='background: #f9f9f9; padding: 15px; border-radius: 5px;'>
                    <p><strong>Ticket Number:</strong> {$ticket['ticket_no']}</p>
                    <p><strong>Status Changed To:</strong> $new_status</p>
                    <p><strong>Resolution Notes:</strong></p>
                    <div style='white-space: pre-line; background: #fff; padding: 10px; border-radius: 3px;'>
                        ".nl2br(htmlspecialchars($resolution))."
                    </div>
                </div>
                <p style='margin-top: 20px; font-size: 12px; color: #777;'>
                    Please do not reply to this automated message.
                </p>
            </div>
        ";
        
        $mail->AltBody = "Your ticket #{$ticket['ticket_no']} status has been updated to $new_status.\n\nResolution Notes:\n$resolution";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>