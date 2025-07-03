<?php
// 🔌 DATABASE CONNECTION (use your Railway DB credentials)
$host = "crossover.proxy.rlwy.net";
$port = 32488;
$username = "root";
$password = "OHtebhVoTYDpgZgrVwjtJJnBDnAUGScb";
$database = "railway";
$conn = new mysqli($host, $username, $password, $database, $port);
if ($conn->connect_error) die("DB connection failed: " . $conn->connect_error);

// ➕ ADD SERVICE
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $email = $_POST['team_email'] ?? null;
    $stmt = $conn->prepare("INSERT INTO service_types (name, team_email) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ✏️ EDIT SERVICE
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['team_email'];
    $stmt = $conn->prepare("UPDATE service_types SET name = ?, team_email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 🗑️ DELETE SERVICE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM service_types WHERE id = $id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Service Types – Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap & Google Fonts -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f3f4f6;
    }
    .container {
      max-width: 900px;
    }
    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    .table th {
      background: #4f46e5;
      color: white;
    }
    .btn {
      border-radius: 12px;
    }
    .modal-content {
      border-radius: 16px;
    }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="card p-4">
    <h2 class="mb-4 text-center text-primary fw-bold">Service Types Admin Dashboard</h2>

    <!-- Add Form -->
    <form method="post" class="row g-3 mb-4">
      <input type="hidden" name="add" value="1">
      <div class="col-md-4">
        <input type="text" name="name" class="form-control form-control-lg" placeholder="Service Name" required>
      </div>
      <div class="col-md-4">
        <input type="email" name="team_email" class="form-control form-control-lg" placeholder="Team Email (optional)">
      </div>
      <div class="col-md-4 d-grid">
        <button type="submit" class="btn btn-primary btn-lg">+ Add Service</button>
      </div>
    </form>

    <!-- Service Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover bg-white rounded">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Team Email</th>
            <th style="width: 160px;">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $result = $conn->query("SELECT * FROM service_types ORDER BY id DESC");
          while ($row = $result->fetch_assoc()):
        ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['team_email']) ?></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
              <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?')">Delete</a>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
              <form method="post" class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Service</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="edit" value="1">
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                  <div class="mb-3">
                    <label class="form-label">Service Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Team Email</label>
                    <input type="email" name="team_email" class="form-control" value="<?= htmlspecialchars($row['team_email']) ?>">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Update</button>
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
              </form>
            </div>
          </div>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
