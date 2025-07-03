<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $ticket_id = intval($_POST['ticket_id']);

    $stmt = $conn->prepare("DELETE FROM tickets WHERE id = ?");
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();

    header("Location: admin_panel.php?deleted=1");
    exit;
}
?>
