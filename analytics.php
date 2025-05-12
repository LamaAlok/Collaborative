<?php
require 'db.php';

// Total pickups
$total_pickups_query = $conn->query("SELECT COUNT(*) as total FROM pickup_requests");
$total_pickups = $total_pickups_query->fetch_assoc()['total'];

// Total waste collected
$total_waste_query = $conn->query("SELECT SUM(quantity) as total FROM pickup_requests");
$total_waste = $total_waste_query->fetch_assoc()['total'];

// Recyclable waste
$recyclable_query = $conn->query("SELECT SUM(quantity) as recyclable FROM pickup_requests WHERE category = 'Recyclable'");
$recyclable = $recyclable_query->fetch_assoc()['recyclable'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Analytics & Report</title>
  <link rel="stylesheet" href="admin.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .chart-container {
    width: 100%;
    max-width: 750px;  /* Slightly reduced from 900px */
    height: 360px;     /* Slightly reduced from 400px */
    margin: 0;
  }

  .summary-box {
    background-color: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    min-width: 250px;
    margin-top: 20px;
  }
    .summary p {
      font-size: 18px;
      margin-bottom: 10px;
    }

    .summary strong {
      font-size: 24px;
    }

    .analytics-wrapper {
      padding: 40px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      flex-wrap: wrap;
    }

    .summary-box {
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      min-width: 250px;
    }
  </style>
</head>
<body>
<div class="dashboard">
  <header class="header">
    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/2a3e6344323cfc3254a3e0afd12c3fe2402a8aa9" alt="Admin" class="header-logo" />
    <h1 class="header-title">Analytics & Report</h1>
  </header>

  <div class="dashboard-content">
    <aside class="sidebar">
      <ul class="sidebar-menu">
  <li><a href="admin.php" class="active">Dashboard</a></li>
  <li><a href="manage-users.php">Manage User</a></li>
  <li><a href="analytics.php">Analytics & Report</a></li>
</ul>

    </aside>

    <main class="main-content">
      <h2 class="section-title">Waste Collection (kg)</h2>

      <div class="analytics-wrapper">
        <div class="chart-container">
          <canvas id="wasteChart"></canvas>
        </div>

        <div class="summary-box">
          <p><strong>Total Pickups:</strong><br><?= $total_pickups ?></p>
          <p><strong>Total Waste Collected:</strong><br><?= $total_waste ?> kg</p>
          <p><strong>Recyclable Waste:</strong><br><?= $recyclable ?> kg ♻️</p>
        </div>
      </div>
    </main>
  </div>
</div>

<script>
const ctx = document.getElementById('wasteChart').getContext('2d');
const wasteChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [
      {
        label: 'Biodegradable',
        borderColor: 'green',
        backgroundColor: 'transparent',
        data: [0, 0, 20, 310, 55, 90]
      },
      {
        label: 'Non-Biodegradable',
        borderColor: 'blue',
        backgroundColor: 'transparent',
        data: [0, 0, 10, 210, 60, 0]
      },
      {
        label: 'Recyclable',
        borderColor: 'teal',
        backgroundColor: 'transparent',
        data: [0, 0, 0, 60, 60, 0]
      }
    ]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    elements: {
      line: {
        tension: 0.4
      }
    }
  }
});
</script>
</body>
</html>
