<?php
include 'db_connect.php';

if (isset($_GET['ticket_no'])) {
    $ticket_no = $conn->real_escape_string($_GET['ticket_no']);
    $ticket = $conn->query("SELECT * FROM tickets WHERE ticket_no = '$ticket_no'")->fetch_assoc();
    
    if ($ticket) {
        echo '<div class="ticket-details">';
        echo '<h4>Ticket #' . htmlspecialchars($ticket['ticket_no']) . '</h4>';
        echo '<p><strong>Name:</strong> ' . htmlspecialchars($ticket['name']) . '</p>';
        echo '<p><strong>Email:</strong> ' . htmlspecialchars($ticket['email']) . '</p>';
        echo '<p><strong>Service Type:</strong> ' . htmlspecialchars($ticket['service_type']) . '</p>';
        echo '<p><strong>Status:</strong> <span class="status-badge status-' . strtolower(str_replace(' ', '', $ticket['status'])) . '">' . htmlspecialchars($ticket['status']) . '</span></p>';
        echo '<p><strong>Created:</strong> ' . date('M d, Y H:i', strtotime($ticket['created_at'])) . '</p>';
        if ($ticket['updated_at']) {
            echo '<p><strong>Last Updated:</strong> ' . date('M d, Y H:i', strtotime($ticket['updated_at'])) . '</p>';
        }
        echo '</div>';
        
        echo '<div class="description mb-4">';
        echo '<h5>Description</h5>';
        echo '<div class="border p-3">' . nl2br(htmlspecialchars($ticket['description'])) . '</div>';
        echo '</div>';
        
        // Status update form
        echo '<form method="post" action="admin_panel.php" class="mb-4">';
        echo '<h5>Update Status</h5>';
        echo '<input type="hidden" name="ticket_no" value="' . htmlspecialchars($ticket['ticket_no']) . '">';
        echo '<div class="row g-3 align-items-center">';
        echo '<div class="col-auto">';
        echo '<select name="status" class="form-select" required>';
        echo '<option value="Open"' . ($ticket['status'] == 'Open' ? ' selected' : '') . '>Open</option>';
        echo '<option value="In Progress"' . ($ticket['status'] == 'In Progress' ? ' selected' : '') . '>In Progress</option>';
        echo '<option value="Resolved"' . ($ticket['status'] == 'Resolved' ? ' selected' : '') . '>Resolved</option>';
        echo '</select>';
        echo '</div>';
        echo '<div class="col-auto">';
        echo '<button type="submit" name="update_status" class="btn btn-primary">Update</button>';
        echo '</div>';
        echo '</div>';
        echo '</form>';
        
        // Delete form
        echo '<form method="post" action="admin_panel.php" onsubmit="return confirm(\'Are you sure you want to delete this ticket?\')">';
        echo '<input type="hidden" name="ticket_no" value="' . htmlspecialchars($ticket['ticket_no']) . '">';
        echo '<button type="submit" name="delete_ticket" class="btn btn-danger">Delete Ticket</button>';
        echo '</form>';
    } else {
        echo '<div class="alert alert-danger">Ticket not found.</div>';
    }
} else {
    echo '<div class="alert alert-danger">No ticket specified.</div>';
}
?>