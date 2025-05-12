<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.html");
    exit();
}

// Database connection
include('db.php'); // assuming this file contains the database connection

// Queries to get the data
$active_users_query = "SELECT COUNT(*) FROM users";
$pickup_approved_query = "SELECT COUNT(*) FROM pickups WHERE status = 'Approved'";
$total_pending_pickups_query = "SELECT COUNT(*) FROM pickups WHERE status = 'Pending'";

// Queries to get the total waste for each category
$biodegradable_waste_query = "SELECT SUM(quantity) FROM pickup_requests WHERE category = 'Biodegradable'";
$non_biodegradable_waste_query = "SELECT SUM(quantity) FROM pickup_requests WHERE category = 'Non-Biodegradable'";
$recyclable_waste_query = "SELECT SUM(quantity) FROM pickup_requests WHERE category = 'Recyclable'";

$active_users_result = mysqli_query($conn, $active_users_query);
$pickup_approved_result = mysqli_query($conn, $pickup_approved_query);
$total_pending_pickups_result = mysqli_query($conn, $total_pending_pickups_query);

// Fetch data for active users, pickups approved, and pending pickups
$active_users = mysqli_fetch_array($active_users_result)[0];
$pickup_approved = mysqli_fetch_array($pickup_approved_result)[0];
$total_pending_pickups = mysqli_fetch_array($total_pending_pickups_result)[0];

// Fetch data for total waste collected by category
$biodegradable_waste_result = mysqli_query($conn, $biodegradable_waste_query);
$non_biodegradable_waste_result = mysqli_query($conn, $non_biodegradable_waste_query);
$recyclable_waste_result = mysqli_query($conn, $recyclable_waste_query);

$biodegradable_waste = mysqli_fetch_array($biodegradable_waste_result)[0] ?? 0;
$non_biodegradable_waste = mysqli_fetch_array($non_biodegradable_waste_result)[0] ?? 0;
$recyclable_waste = mysqli_fetch_array($recyclable_waste_result)[0] ?? 0;

// Close the database connection
mysqli_close($conn);
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Hanuman:wght@400;700&family=Instrument+Sans:wght@400;500;700&display=swap"
      rel="stylesheet"
    />
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
  <li><a href="admin.php" class="active">Dashboard</a></li>
  <li><a href="manage-users.php">Manage User</a></li>
  <li><a href="analytics.php">Analytics & Report</a></li>
</ul>

        </aside>

        <main class="main-content">
          <section class="overview">
            <h2 class="section-title">Overview</h2>
            <div class="card-grid">
              <div class="card">
                <span class="icon">👤</span>
                <p class="card-title">Active Users</p>
                <h3 class="card-number"><?php echo $active_users; ?></h3>
              </div>
              <div class="card">
                <span class="icon">♿</span>
                <p class="card-title">Pickup Approved</p>
                <h3 class="card-number"><?php echo $pickup_approved; ?></h3>
              </div>
              <div class="card">
                <span class="icon">🧾</span>
                <p class="card-title">Total pending Pickups</p>
                <h3 class="card-number"><?php echo $total_pending_pickups; ?></h3>
              </div>
            </div>
          </section>

          <section class="waste-section">
            <h2 class="section-title">Total Waste Collected</h2>
            <div class="card-grid waste">
              <div class="waste-card">
                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/faed4867793e776ffa7c83bfc5f8841c784135dc" alt="Biodegradable" />
                <p class="waste-type">Biodegradable</p>
                <h3 class="waste-amount"><?php echo $biodegradable_waste; ?> kg</h3>
              </div>
              <div class="waste-card">
                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/211f3bcec3e008b4f80ef556a4a3bfa218037213" alt="Non-Biodegradable" />
                <p class="waste-type">Non-Biodegradable</p>
                <h3 class="waste-amount"><?php echo $non_biodegradable_waste; ?> kg</h3>
              </div>
              <div class="waste-card">
                <svg width="60" height="60" fill="#44A355" viewBox="0 0 24 24">
                  <path d="M21.561 6.685l-1.584-2.774a1 1 0 0 0-1.731 1l1.584 2.774a1 1 0 0 0 1.731-1zM3.439 6.685a1 1 0 0 0 1.731 1l1.584-2.774a1 1 0 1 0-1.731-1L3.439 6.685z"/>
                  <path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2zm1 14.93a7.984 7.984 0 0 1-2-.262V20h2z"/>
                </svg>
                <p class="waste-type">Recyclable</p>
                <h3 class="waste-amount"><?php echo $recyclable_waste; ?> kg</h3>
              </div>
            </div>
          </section>
        </main>
      </div>
    </div>
  </body>
</html>
