<?php
session_start();
require 'db.php'; // contains your DB connection logic

// Check if the user is logged in and is a collector
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'collector') {
    header("Location: login.html");
    exit();
}

// Fetch pickups from the database
$sql = "SELECT * FROM pickups ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Collector Dashboard</title>
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
              <button class="approve-btn"><span>Approve</span></button>
              <button class="reject-btn">Reject</button>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </section>
  </main>
</div>
</body>
</html>
