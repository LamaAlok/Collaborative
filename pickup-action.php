<?php
session_start();
require 'db.php';

// Check if the user is logged in and is a collector
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'collector') {
    header("Location: login.html");
    exit();
}

// Handle pickup status update via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pickup_id'], $_POST['status'])) {
    $pickup_id = $_POST['pickup_id'];
    $status = $_POST['status'];

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

// Fetch pickup requests
$sql = "SELECT * FROM pickups ORDER BY id DESC";
$result = $conn->query($sql);

// Check current collector status (from session or DB if implemented)
$collectorStatus = isset($_SESSION['collector_status']) ? $_SESSION['collector_status'] : 'off';
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
      <li class="menu-item selected">Manage Pickups</li>
    </ul>
  </nav>

  <main class="main-content">
    <!-- Toggle Collector Mode -->
    <div style="margin-bottom: 20px;">
      <label>
        <input type="checkbox" id="collectorToggle" <?= $collectorStatus === 'on' ? 'checked' : '' ?> />
        <strong>Collector Receiving Requests</strong>
      </label>
    </div>

    <!-- Off Message -->
    <div id="offMessage" style="display: <?= $collectorStatus === 'on' ? 'none' : 'block' ?>;">
      <h2 style="color: gray;">Please Turn on Receiving requests</h2>
    </div>

    <!-- Search + Pickup Table -->
    <div class="search-container" style="display: <?= $collectorStatus === 'on' ? 'flex' : 'none' ?>;">
      <input type="text" placeholder="Search......." class="search-input" />
    </div>

    <section class="pickups-table-container" id="pickupTable" style="display: <?= $collectorStatus === 'on' ? 'block' : 'none' ?>;">
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
      if (response.includes("successfully")) {
        statusCell.innerText = newStatus;
        statusCell.className = `table-cell status ${newStatus.toLowerCase()}`;
        row.querySelectorAll('button').forEach(btn => btn.disabled = true);
        setTimeout(() => {
          row.style.opacity = 0;
          setTimeout(() => row.remove(), 200);
        }, 2000);
      } else {
        alert("Error: " + response);
      }
    })
    .catch(err => alert("Failed to update status."));
  }

  document.querySelector('.search-input').addEventListener('input', function () {
    const query = this.value.toLowerCase();
    const rows = document.querySelectorAll('.table-row');
    rows.forEach(row => {
      const pickupId = row.querySelector('.pickup-id').innerText.toLowerCase();
      const location = row.querySelector('.location').innerText.toLowerCase();
      row.style.display = pickupId.includes(query) || location.includes(query) ? '' : 'none';
    });
  });

  // Collector toggle logic
  document.getElementById('collectorToggle').addEventListener('change', function () {
    const status = this.checked ? 'on' : 'off';
    fetch('toggle_collector_status.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'status=' + status
    }).then(() => {
      document.getElementById('offMessage').style.display = status === 'on' ? 'none' : 'block';
      document.querySelector('.search-container').style.display = status === 'on' ? 'flex' : 'none';
      document.getElementById('pickupTable').style.display = status === 'on' ? 'block' : 'none';
    });
  });
</script>
</body>
</html>
