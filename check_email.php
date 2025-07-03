<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $conn->real_escape_string($_POST['email']);

    $query = "SELECT id FROM tickets WHERE email = ? AND status NOT IN ('Resolved', 'Closed')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    echo $stmt->num_rows > 0 ? 'exists' : 'ok';
}
?>
