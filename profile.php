<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: newloginpage.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone);
$stmt->fetch();
$stmt->close();

// Fetch waste stats
$stmt = $conn->prepare("
  SELECT 
    SUM(CASE WHEN category = 'Recyclable' THEN quantity ELSE 0 END),
    SUM(CASE WHEN category = 'Biodegradable' THEN quantity ELSE 0 END),
    SUM(CASE WHEN category = 'Non-Biodegradable' THEN quantity ELSE 0 END),
    SUM(quantity)
  FROM pickup_requests
  WHERE user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($recyclable, $biodegradable, $non_biodegradable, $total);
$stmt->fetch();
$stmt->close();
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profile - The Bin Buddy</title>
    <link rel="stylesheet" href="profile.css" />
  </head>
  <body>
    <main class="profile-container">
      <header class="navigation-header">
        <div class="logo-container">
          <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/75865e3ae523461c58906f34e844609394b4984f?placeholderIfAbsent=true&apiKey=e98cb879b9a34c988621d0f069c4a3eb" alt="Bin Buddy Logo" class="logo-image" />
          <h1 class="logo-text">THE BIN-BUDDY</h1>
        </div>
        <nav class="main-nav">
          <a href="dashboardB.php" class="nav-link">Home</a>
          <a href="profile.php" class="nav-link">Profile</a>
          <a href="service.php" class="nav-link">Service</a>
          <a href="help.html" class="nav-link">Contact</a>
        </nav>
      </header>

      <hr class="divider" />

      <div class="main-content">
        <div class="content-wrapper">
          <section class="user-profile">
            <div class="profile-details">
              <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/e58ebad79b1c662957c225d15b648004304621b8?placeholderIfAbsent=true&apiKey=e98cb879b9a34c988621d0f069c4a3eb" alt="Profile Picture" class="profile-picture" />
              <h2 class="user-name"><?php echo htmlspecialchars($name); ?></h2>
              <div class="contact-info">
                <p class="email"><?php echo htmlspecialchars($email); ?></p>
                <div class="phone-container">
                  <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/98a0003ded3d66ddd14938d7791ba137e3f05ad6?placeholderIfAbsent=true&apiKey=e98cb879b9a34c988621d0f069c4a3eb" alt="Phone Icon" class="icon" />
                  <p class="phone"><?php echo htmlspecialchars($phone); ?></p>
                </div>
                <div class="address-container">
                  <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/d6f6d5b8f214d752675ab345ed959da8cabeebf9?placeholderIfAbsent=true&apiKey=e98cb879b9a34c988621d0f069c4a3eb" alt="Location Icon" class="icon" />
                  <p class="address">-Naxal,Kathmandu</p>
                </div>
              </div>
              <div class="waste-stats">
                <div class="stats-content">
                  <p>Total Waste</p>
                  <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/7e5709c50f7210628f8775ecbac0072feb58bf21?placeholderIfAbsent=true&apiKey=e98cb879b9a34c988621d0f069c4a3eb" alt="Waste Icon" class="waste-icon" />
                  <p class="waste-amount"><?php echo $total ?? 0; ?> kg</p>
                </div>
              </div>
            </div>
          </section>

          <section class="activities-section">
            <div class="activities-container">
              <h2 class="section-title">Recent Activity</h2>

              <article class="activity-card">
                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/c9df8ab77755bafe66923a65a1c3f7addfd1d8ed?placeholderIfAbsent=true&apiKey=e98cb879b9a34c988621d0f069c4a3eb" alt="Recycling Icon" class="activity-icon" />
                <div class="activity-details">
                  <h3 class="activity-title">Recycling collection</h3>
                  <p class="activity-description">
                    <?php echo $recyclable ?? 0; ?> kg of Recyclables Collected
                  </p>
                </div>
              </article>

              <article class="activity-card">
                <img
                  src="https://cdn.builder.io/api/v1/image/assets/TEMP/d81d77a4bb444dd8e1b11c47a379f4cfa10a4604?placeholderIfAbsent=true&apiKey=e98cb879b9a34c988621d0f069c4a3eb"
                  alt="Biodegradable Icon"
                  class="activity-icon"
                />
                <div class="activity-details">
                  <h3 class="activity-title">Biodegradable collection</h3>
                  <p class="activity-description">
                    <?php echo $biodegradable ?? 0; ?> kg of Biodegradable Collected
                  </p>
                </div>
              </article>

              <div class="separator"></div>

              <article class="activity-card">
                <img
                  src="https://cdn.builder.io/api/v1/image/assets/TEMP/08f2022d1af38d32ff304cee1b2ada05ee46a24c?placeholderIfAbsent=true&apiKey=e98cb879b9a34c988621d0f069c4a3eb"
                  alt="Non-Biodegradable Icon"
                  class="activity-icon"
                />
                <div class="activity-details">
                  <h3 class="activity-title">Non-Biodegradable collection</h3>
                  <p class="activity-description">
                    <?php echo $non_biodegradable ?? 0; ?> kg of Non-Biodegradable Collected
                  </p>
                </div>
              </article>

              <button class="logout-button" onclick="window.location.href='logout.php'">
                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/f02c540917b2ff5e4ca656d44c8599dcf3aabcb9?placeholderIfAbsent=true&apiKey=e98cb879b9a34c988621d0f069c4a3eb" alt="Logout Icon" class="logout-icon" />
                <span>Logout</span>
              </button>
            </div>
          </section>
        </div>
      </div>
    </main>
    <script src="profile.js"></script>
  </body>
</html>
