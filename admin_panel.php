<?php
session_start();
include 'db_connect.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to send emails
function sendPHPMailerEmail($to, $subject, $message) {
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
        $mail->Timeout    = 10;

        // Sender & recipient
        $mail->setFrom('v.gugan16@gmail.com', 'Sunface Support');
        $mail->addAddress($to);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <h2 style="color: #007bff;">'.$subject.'</h2>
                <div style="white-space: pre-line; line-height: 1.6; padding: 15px; background: #f9f9f9; border-radius: 5px;">
                    '.nl2br(htmlspecialchars($message)).'
                </div>
                <p style="margin-top: 20px; font-size: 12px; color: #777;">
                    Please do not reply to this automated message.
                </p>
            </div>
        ';
        $mail->AltBody = strip_tags($message);

        $mail->send();
        return ['success' => true, 'output' => 'Email sent successfully'];
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $e->getMessage());
        return ['success' => false, 'output' => "Mailer Error: {$e->getMessage()}"];
    }
}

// Function to generate automatic resolution message based on status
function generateAutoResolutionMessage($status) {
    switch ($status) {
        case 'Open':
            return "Your ticket has been opened and is awaiting review by our support team.";
        case 'Inprogress':
            return "Our team is currently working on your ticket. We'll provide updates as soon as possible.";
        case 'Resolved':
            return "Your ticket has been resolved. If you have any further questions, please don't hesitate to open a new ticket.";
        default:
            return "Your ticket status has been updated.";
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_ticket'])) {
        // Update ticket status
        $ticket_id = intval($_POST['ticket_id']);
        $new_status = $conn->real_escape_string($_POST['status']);
        $resolution = isset($_POST['resolution']) ? $conn->real_escape_string($_POST['resolution']) : '';
        $use_auto_message = isset($_POST['use_auto_message']) ? true : false;
        
        // Get current ticket data
        $ticket_query = $conn->query("SELECT * FROM tickets WHERE id = $ticket_id");
        if ($ticket_query->num_rows === 0) {
            die("Ticket not found");
        }
        $ticket = $ticket_query->fetch_assoc();
        
        // If using auto message and no custom resolution provided
        if ($use_auto_message && empty($resolution)) {
            $resolution = generateAutoResolutionMessage($new_status);
        }
        
        // Update the ticket
        $update_query = $conn->prepare("UPDATE tickets SET status = ?, resolution = ? WHERE id = ?");
        $update_query->bind_param("ssi", $new_status, $resolution, $ticket_id);
        
        if ($update_query->execute()) {
            // Send notification emails if status changed or resolution updated
            if ($ticket['status'] != $new_status || $ticket['resolution'] != $resolution) {
                // Email to client
                $client_subject = "Ticket #{$ticket['ticket_no']} Status Updated";
                $client_message = "Dear {$ticket['name']},\n\n" .
                                 "The status of your ticket has been updated.\n\n" .
                                 "Ticket Details:\n----------------\n" .
                                 "Ticket Number: {$ticket['ticket_no']}\n" .
                                 "Service Type: {$ticket['service_type']}\n" .
                                 "New Status: $new_status\n\n";
                
                if (!empty($resolution)) {
                    $client_message .= "Resolution Details:\n$resolution\n\n";
                }
                
                $client_message .= "Thank you for your patience.\n\n" .
                                  "Sunface Support Team";
                
                // Email to admin
                $admin_subject = "Ticket #{$ticket['ticket_no']} Status Updated to $new_status";
                $admin_message = "Ticket status has been updated.\n\n" .
                                "Ticket Details:\n----------------\n" .
                                "Ticket Number: {$ticket['ticket_no']}\n" .
                                "Customer Name: {$ticket['name']}\n" .
                                "Customer Email: {$ticket['email']}\n" .
                                "Service Type: {$ticket['service_type']}\n" .
                                "New Status: $new_status\n\n";
                
                if (!empty($resolution)) {
                    $admin_message .= "Resolution Details:\n$resolution\n\n";
                    if ($use_auto_message) {
                        $admin_message .= "Note: This was an auto-generated resolution message.\n\n";
                    }
                }
                
                // Send emails
                sendPHPMailerEmail($ticket['email'], $client_subject, $client_message);
                sendPHPMailerEmail('pranaesh2004@gmail.com', $admin_subject, $admin_message);
            }
            
            $_SESSION['message'] = "Ticket updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating ticket: " . $conn->error;
        }
        
        header("Location: admin_panel.php");
        exit;
    } elseif (isset($_POST['delete_ticket'])) {
        // Delete ticket
        $ticket_id = intval($_POST['ticket_id']);
        
        // Get ticket data for email before deleting
        $ticket_query = $conn->query("SELECT * FROM tickets WHERE id = $ticket_id");
        if ($ticket_query->num_rows > 0) {
            $ticket = $ticket_query->fetch_assoc();
            
            // Send notification email
            $subject = "Ticket #{$ticket['ticket_no']} Closed";
            $message = "Dear {$ticket['name']},\n\n" .
                       "Your ticket has been closed by our support team.\n\n" .
                       "Ticket Details:\n----------------\n" .
                       "Ticket Number: {$ticket['ticket_no']}\n" .
                       "Service Type: {$ticket['service_type']}\n" .
                       "Status: Closed\n\n" .
                       "If you have any further questions, please don't hesitate to open a new ticket.\n\n" .
                       "Sunface Support Team";
            
            sendPHPMailerEmail($ticket['email'], $subject, $message);
            sendPHPMailerEmail('support@sunface.in', $subject, $message);
        }
        
        // Delete the ticket
        $delete_query = $conn->prepare("DELETE FROM tickets WHERE id = ?");
        $delete_query->bind_param("i", $ticket_id);
        
        if ($delete_query->execute()) {
            $_SESSION['message'] = "Ticket deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting ticket: " . $conn->error;
        }
        
        header("Location: admin_panel.php");
        exit;
    }
}

// Fetch all tickets
$tickets = $conn->query("SELECT * FROM tickets ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Ticket Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .status-open {
            background-color: #d1ecf1;
        }
        .status-inprogress {
            background-color: #fff3cd;
        }
        .status-resolved {
            background-color: #d4edda;
        }
        .ticket-details {
            margin-bottom: 15px;
        }
        .auto-message-toggle {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .auto-message-toggle label {
            margin-left: 10px;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Ticket Management Admin Panel</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">All Tickets</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($tickets->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Ticket #</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Service Type</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($ticket = $tickets->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($ticket['ticket_no']); ?></td>
                                                <td><?= htmlspecialchars($ticket['name']); ?></td>
                                                <td><?= htmlspecialchars($ticket['email']); ?></td>
                                                <td><?= htmlspecialchars($ticket['service_type']); ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?= $ticket['status'] == 'Open' ? 'bg-info' : '' ?>
                                                        <?= $ticket['status'] == 'Inprogress' ? 'bg-warning' : '' ?>
                                                        <?= $ticket['status'] == 'Resolved' ? 'bg-success' : '' ?>">
                                                        <?= htmlspecialchars($ticket['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?= date('M d, Y H:i', strtotime($ticket['created_at'])); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewTicketModal<?= $ticket['id']; ?>">
                                                        View
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editTicketModal<?= $ticket['id']; ?>">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTicketModal<?= $ticket['id']; ?>">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                            
                                            <!-- View Ticket Modal -->
                                            <div class="modal fade" id="viewTicketModal<?= $ticket['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Ticket #<?= $ticket['ticket_no']; ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row ticket-details">
                                                                <div class="col-md-6">
                                                                    <p><strong>Name:</strong> <?= htmlspecialchars($ticket['name']); ?></p>
                                                                    <p><strong>Email:</strong> <?= htmlspecialchars($ticket['email']); ?></p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p><strong>Service Type:</strong> <?= htmlspecialchars($ticket['service_type']); ?></p>
                                                                    <p><strong>Status:</strong> 
                                                                        <span class="badge 
                                                                            <?= $ticket['status'] == 'Open' ? 'bg-info' : '' ?>
                                                                            <?= $ticket['status'] == 'Inprogress' ? 'bg-warning' : '' ?>
                                                                            <?= $ticket['status'] == 'Resolved' ? 'bg-success' : '' ?>">
                                                                            <?= htmlspecialchars($ticket['status']); ?>
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <h6>Description:</h6>
                                                                <div class="p-3 bg-light rounded">
                                                                    <?= nl2br(htmlspecialchars($ticket['description'])); ?>
                                                                </div>
                                                            </div>
                                                            <?php if (!empty($ticket['resolution'])): ?>
                                                                <div class="mb-3">
                                                                    <h6>Resolution:</h6>
                                                                    <div class="p-3 bg-light rounded">
                                                                        <?= nl2br(htmlspecialchars($ticket['resolution'])); ?>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <p><small><strong>Created:</strong> <?= date('M d, Y H:i', strtotime($ticket['created_at'])); ?></small></p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p><small><strong>Last Updated:</strong> <?= $ticket['updated_at'] ? date('M d, Y H:i', strtotime($ticket['updated_at'])) : 'Never'; ?></small></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Edit Ticket Modal -->
                                            <div class="modal fade" id="editTicketModal<?= $ticket['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Ticket #<?= $ticket['ticket_no']; ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" name="ticket_id" value="<?= $ticket['id']; ?>">
                                                                
                                                                <div class="mb-3">
                                                                    <label class="form-label">Status</label>
                                                                    <select name="status" class="form-select" required>
                                                                        <option value="Open" <?= $ticket['status'] == 'Open' ? 'selected' : '' ?>>Open</option>
                                                                        <option value="Inprogress" <?= $ticket['status'] == 'Inprogress' ? 'selected' : '' ?>>Inprogress</option>
                                                                        <option value="Resolved" <?= $ticket['status'] == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                                                    </select>
                                                                </div>
                                                                
                                                                <div class="auto-message-toggle">
                                                                    <input type="checkbox" id="use_auto_message_<?= $ticket['id']; ?>" name="use_auto_message" value="1" checked>
                                                                    <label for="use_auto_message_<?= $ticket['id']; ?>">Use automatic resolution message</label>
                                                                </div>
                                                                
                                                                <div class="mb-3">
                                                                    <label class="form-label">Resolution Details</label>
                                                                    <textarea name="resolution" class="form-control" rows="4" placeholder="Enter custom resolution message (optional)"><?= htmlspecialchars($ticket['resolution'] ?? ''); ?></textarea>
                                                                    <small class="text-muted">Leave blank to use automatic message based on status, or uncheck above to use your own message.</small>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" name="update_ticket" class="btn btn-primary">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Delete Ticket Modal -->
                                            <div class="modal fade" id="deleteTicketModal<?= $ticket['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="">
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title">Confirm Deletion</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" name="ticket_id" value="<?= $ticket['id']; ?>">
                                                                <p>Are you sure you want to delete ticket #<?= $ticket['ticket_no']; ?>?</p>
                                                                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" name="delete_ticket" class="btn btn-danger">Delete Ticket</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">No tickets found.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add JavaScript to toggle resolution textarea based on auto message checkbox
        document.addEventListener('DOMContentLoaded', function() {
            // Get all edit modals
            const editModals = document.querySelectorAll('[id^="editTicketModal"]');
            
            editModals.forEach(modal => {
                const checkbox = modal.querySelector('input[name="use_auto_message"]');
                const textarea = modal.querySelector('textarea[name="resolution"]');
                
                // Initial state
                if (checkbox.checked) {
                    textarea.disabled = true;
                    textarea.placeholder = "Automatic message will be used based on status";
                }
                
                // Add event listener
                checkbox.addEventListener('change', function() {
                    textarea.disabled = this.checked;
                    if (this.checked) {
                        textarea.placeholder = "Automatic message will be used based on status";
                    } else {
                        textarea.placeholder = "Enter custom resolution message";
                    }
                });
            });
        });
    </script>
</body>
</html>