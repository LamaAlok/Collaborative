<?php
session_start();
require 'db.php';
include 'nav.php'; 

// Check if it's an AJAX POST request to update pickup status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pickup_id'], $_POST['status'])) {
    $pickup_id = $_POST['pickup_id'];
    $status = $_POST['status'];
    file_put_contents("debug.log", json_encode($_POST) . PHP_EOL, FILE_APPEND);

    if (empty($pickup_id) || empty($status)) {
        echo "Missing pickup_id or status.";
        exit;
    }

    $stmt = $conn->prepare("UPDATE pickups SET status = ? WHERE pickup_id = ?");
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        exit;
    }

    $stmt->bind_param("ss", $status, $pickup_id);
    if ($stmt->execute()) {
        echo "Status updated successfully.";
    } else {
        echo "Failed to update status.";
    }
    exit;
}

// Proceed with loading the HTML interface
$sql = "SELECT * FROM pickups ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Collector Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="pickup.css" />
  <link href="https://fonts.googleapis.com/css2?family=Hanuman:wght@400;700&display=swap" rel="stylesheet"/>
</head>
<body>
<div class="dashboard-container">
  <nav class="sidebar">
    <h1 class="sidebar-title">Manage Pickups</h1>
    <ul class="sidebar-menu">
      <li class="menu-item active">Dashboard</li>
      <li class="menu-item selected">Manage Pickups</li>
      <li class="menu-item">Manage User</li>
      <li class="menu-item">Analytics &amp; Report</li>
      <li class="menu-item">Settings</li>
    </ul>
  </nav>

  <main class="main-content">
    <div class="search-container">
      <input type="text" placeholder="Search......." class="search-input" />
    </div>

    <section class="pickups-table-container">
      <header class="table-header">
        <div class="header-row">
          <h3 class="header-cell">Pickup ID</h3>
          <h3 class="header-cell">Location</h3>
          <h3 class="header-cell">Status</h3>
          <h3 class="header-cell">Actions</h3>
        </div>
      </header>

      <div class="table-body">
        <?php while($row = $result->fetch_assoc()): ?>
          <div class="table-row">
            <div class="table-cell pickup-id"><?= htmlspecialchars($row['pickup_id']) ?></div>
            <div class="table-cell location"><?= htmlspecialchars($row['location']) ?></div>
            <div class="table-cell status <?= strtolower($row['status']) ?>">
              <?= htmlspecialchars($row['status']) ?>
            </div>
            <div class="table-cell actions">
              <button class="approve-btn" onclick="updateStatus(this, 'Approved')">Approve</button>
              <button class="reject-btn" onclick="updateStatus(this, 'Rejected')">Reject</button>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </section>
  </main>
</div>

<script>
  function updateStatus(button, newStatus) {
  const row = button.closest('.table-row');
  const pickupId = row.querySelector('.pickup-id').innerText.trim();
  const statusCell = row.querySelector('.status');

  fetch('pickup-action.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `pickup_id=${pickupId}&status=${newStatus}`
  })
  .then(res => res.text())
  .then(response => {
    console.log(response);
    if (response.includes("successfully")) {
      // Update status text and color
      statusCell.innerText = newStatus;
      statusCell.className = `table-cell status ${newStatus.toLowerCase()}`;

      // Optionally disable buttons
      const buttons = row.querySelectorAll('button');
      buttons.forEach(btn => btn.disabled = true);

      // Remove row after 5 seconds
      setTimeout(() => {
        row.style.transition = 'opacity 0.2s';
        row.style.opacity = 0;
        setTimeout(() => row.remove(), 200);
      }, 2000);
    } else {
      alert("Error: " + response);
    }
  })
  .catch(err => {
    console.error(err);
    alert("Failed to update status.");
  });
}

</script>
<script>
  document.querySelector('.search-input').addEventListener('input', function () {
    const query = this.value.toLowerCase();
    const rows = document.querySelectorAll('.table-row');

    rows.forEach(row => {
      const pickupId = row.querySelector('.pickup-id').innerText.toLowerCase();
      const location = row.querySelector('.location').innerText.toLowerCase();

      if (pickupId.includes(query) || location.includes(query)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  });
</script>

</body>
</html>
