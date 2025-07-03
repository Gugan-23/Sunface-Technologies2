<?php
include 'db_connect.php';
include 'cloudinary_config.php';

// Handle Delete
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("SELECT image FROM plans WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imageName);
    $stmt->fetch();
    $stmt->close();
    if ($imageName && file_exists("uploads/$imageName")) {
        unlink("uploads/$imageName");
    }
    $stmt = $conn->prepare("DELETE FROM plans WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit;
}

// Handle Add/Edit
$editData = null;
if (isset($_GET['edit_id'])) {
    $id = intval($_GET['edit_id']);
    $stmt = $conn->prepare("SELECT * FROM plans WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editData = $stmt->get_result()->fetch_assoc();
}

// Handle Save (Insert/Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_name = $_POST['plan_name'];
    $price = $_POST['price'];
    $validity = $_POST['validity'];
    $description = $_POST['description'];
    $addons = $_POST['addons'];
    $category = $_POST['category'];
    $image = $_FILES['image']['name'] ?? '';
    $image_path = '';
    $image_to_save = '';

    if (!empty($image)) {
        $cloud_name = CLOUDINARY_CLOUD_NAME;
        $api_key = CLOUDINARY_API_KEY;
        $api_secret = CLOUDINARY_API_SECRET;

        $file = $_FILES['image']['tmp_name'];
        $timestamp = time();

        // Parameters to sign
        $params_to_sign = [
            'timestamp' => $timestamp,
        ];
        // Create the signature string
        $signature_string = http_build_query($params_to_sign) . $api_secret;
        $signature = sha1("timestamp=$timestamp$api_secret");

        $ch = curl_init();
        $data = [
            'file' => new CURLFile($file),
            'api_key' => $api_key,
            'timestamp' => $timestamp,
            'signature' => $signature,
        ];
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/$cloud_name/image/upload");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);
        if (isset($responseData['secure_url'])) {
            $image_to_save = $responseData['secure_url'];
        } else {
            $image_to_save = '';
        }
    }

    if (isset($_POST['plan_id'])) {
        // Update
        $id = intval($_POST['plan_id']);
        if ($image_to_save) {
            // Delete old image
            $stmt = $conn->prepare("SELECT image FROM plans WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($oldImage);
            $stmt->fetch();
            $stmt->close();
            if ($oldImage && file_exists("uploads/$oldImage")) {
                unlink("uploads/$oldImage");
            }
            $query = "UPDATE plans SET plan_name=?, price=?, validity=?, description=?, addons=?, category=?, image=? WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sdsssssi", $plan_name, $price, $validity, $description, $addons, $category, $image_to_save, $id);
        } else {
            $query = "UPDATE plans SET plan_name=?, price=?, validity=?, description=?, addons=?, category=? WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sdssssi", $plan_name, $price, $validity, $description, $addons, $category, $id);
        }
        $stmt->execute();
    } else {
        // Insert
        $query = "INSERT INTO plans (plan_name, price, validity, description, addons, category, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
$stmt->bind_param("sdsssss", $plan_name, $price, $validity, $description, $addons, $category, $image_to_save);
        $stmt->execute();
    }
    header("Location: admin_dashboard.php");
    exit;
}

// Fetch all plans for display
$result = $conn->query("SELECT * FROM plans ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Plans</title>
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/services.css" />
    <link rel="stylesheet" href="css/admin_dashboard.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <h2 style="text-align:center; margin-top:30px;">
        <?= $editData ? 'Edit Plan' : 'Add New Plan' ?>
    </h2>
    <div style="position:fixed; top:20px; left:20px; z-index:1000; display:flex; flex-direction:column; gap:12px;">
        <a href="admin_panel.php" style="padding:10px 18px; background:#101d42; color:#fff; border-radius:6px; text-decoration:none; font-weight:600; box-shadow:0 2px 8px #0001;">Tickets Managing</a>
        <a href="ticket_service.php" style="padding:10px 18px; background:#007bff; color:#fff; border-radius:6px; text-decoration:none; font-weight:600; box-shadow:0 2px 8px #0001;">Add Service Type</a>
    </div>
    <form class="admin-form" method="POST" enctype="multipart/form-data" action="admin_dashboard.php<?= $editData ? '?edit_id='.$editData['id'] : '' ?>">
        <?php if ($editData): ?>
            <input type="hidden" name="plan_id" value="<?= $editData['id'] ?>">
        <?php endif; ?>
        <input type="text" name="plan_name" placeholder="Plan Name" value="<?= htmlspecialchars($editData['plan_name'] ?? '') ?>" required>
        <input type="number" step="0.01" name="price" placeholder="Price" value="<?= htmlspecialchars($editData['price'] ?? '') ?>" required>
        <input type="text" name="validity" placeholder="Validity" value="<?= htmlspecialchars($editData['validity'] ?? '') ?>" required>
        
        <label for="description" style="font-weight:600;margin-bottom:4px;">Description</label>
        <textarea name="description" id="description" placeholder="Description" rows="6" style="resize:vertical;min-height:100px;"><?= htmlspecialchars($editData['description'] ?? '') ?></textarea>
        
        <label for="addons" style="font-weight:600;margin-bottom:4px;">Add-ons</label>
        <textarea name="addons" id="addons" placeholder="Add-ons" rows="4" style="resize:vertical;min-height:60px;"><?= htmlspecialchars($editData['addons'] ?? '') ?></textarea>
        
        <select name="category" required>
            <option value="">-- Select Category --</option>
            <option value="Website" <?= ($editData['category'] ?? '') === 'Website' ? 'selected' : '' ?>>Website</option>
            <option value="Mobile App" <?= ($editData['category'] ?? '') === 'Mobile App' ? 'selected' : '' ?>>Mobile App</option>
            <option value="Digital Marketing" <?= ($editData['category'] ?? '') === 'Digital Marketing' ? 'selected' : '' ?>>Digital Marketing</option>
        </select>
        <input type="file" name="image" id="image" accept="image/*" onchange="previewImage()">
        <img id="preview" src="<?= isset($editData['image']) && $editData['image'] ? 'uploads/'.htmlspecialchars($editData['image']) : '#' ?>" style="<?= isset($editData['image']) && $editData['image'] ? '' : 'display:none;' ?>">
        <button type="submit"><?= $editData ? 'Update' : 'Save' ?> Plan</button>
        <?php if ($editData): ?>
            <a href="admin_dashboard.php" style="display:inline-block; margin-top:10px; color:#101d42;">Cancel Edit</a>
        <?php endif; ?>
    </form>

    <h2 style="text-align:center; margin:40px 0 20px 0;">All Plans</h2>
    <table class="plans-table">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price (₹)</th>
            <th>Validity</th>
            <th>Description</th>
            <th>Add-ons</th>
            <th>Category</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['plan_name']) ?></td>
            <td><?= htmlspecialchars($row['price']) ?></td>
            <td><?= htmlspecialchars($row['validity']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
            <td><?= nl2br(htmlspecialchars($row['addons'])) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td>
                <?php if ($row['image']): ?>
                    <img src="<?= htmlspecialchars($row['image']) ?>" alt="Plan Image">
                <?php else: ?>
                    <span style="color:#aaa;">No Image</span>
                <?php endif; ?>
            </td>
            <td class="action-links">
                <a href="admin_dashboard.php?edit_id=<?= $row['id'] ?>">Edit</a> |
                <a href="admin_dashboard.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this plan?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <script>
    function previewImage() {
        const preview = document.getElementById('preview');
        const file = document.getElementById('image').files[0];
        if (!file) {
            preview.style.display = 'none';
            preview.src = '#';
            return;
        }
        const reader = new FileReader();
        reader.onloadend = () => {
            preview.src = reader.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
    </script>
</body>
</html>
