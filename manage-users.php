<?php
require 'db.php';

$users = [];
$message = '';

// Update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $user_type = $_POST['user_type'];

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, user_type = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $name, $email, $phone, $user_type, $id);

    if ($stmt->execute()) {
        $message = "User ID $id updated successfully.";
    } else {
        $message = "Error updating user: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch users
$result = $conn->query("SELECT * FROM users ORDER BY id ASC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Users</title>
  <link rel="stylesheet" href="manage-users.css" />
</head>
<body>
  <div class="dashboard">
    <header class="header">
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/2a3e6344323cfc3254a3e0afd12c3fe2402a8aa9" alt="Admin" class="header-logo" />
      <h1 class="header-title">Admin Dashboard</h1>
    </header>

    <div class="dashboard-content">
      <aside class="sidebar">
        <ul class="sidebar-menu">
          <li><a href="admin.php">Dashboard</a></li>
          <li class="active"><a href="manage-users.php">Manage User</a></li>
          <li><a href="analytics.php">Analytics & Report</a></li>
        </ul>
      </aside>

      <main class="main-content">
        <h2 class="section-title">Edit Users</h2>
        <?php if ($message): ?>
          <p style="color: green; margin-bottom: 20px;"><?= $message ?></p>
        <?php endif; ?>

        <?php if (count($users) > 0): ?>
          <?php foreach ($users as $user): ?>
            <form method="POST" class="edit-form" style="margin-bottom: 30px; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 600px;">
              <input type="hidden" name="id" value="<?= $user['id'] ?>">
              <label>Name</label>
              <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required />

              <label>Email</label>
              <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required />

              <label>Phone</label>
              <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" />

              <label>Role</label>
              <select name="user_type">
                <option value="admin" <?= $user['user_type'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="collector" <?= $user['user_type'] === 'collector' ? 'selected' : '' ?>>Collector</option>
                <option value="user" <?= $user['user_type'] === 'user' ? 'selected' : '' ?>>User</option>
              </select>

              <div class="buttons" style="margin-top: 15px;">
                <button type="submit" name="update_user" class="save-btn" style="background: #4ab857; color: #fff; padding: 10px 20px; border: none; border-radius: 5px;">Save Changes</button>
              </div>
            </form>
          <?php endforeach; ?>
        <?php else: ?>
          <p>User not found.</p>
        <?php endif; ?>
      </main>
    </div>
  </div>
</body>
</html>
