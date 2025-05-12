<?php
session_start();
require 'db.php'; // DB connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('You must be logged in to request a pickup.'); window.location.href = 'login.html';</script>";
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $waste = $_POST['waste'];
    $quantity = $_POST['quantity'];

    if ($date && $time && $location && $waste && $quantity) {
        // Insert into pickup_requests
        $stmt1 = $conn->prepare("INSERT INTO pickup_requests (user_id, date, time, location, category, quantity, requested_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt1->bind_param("isssss", $user_id, $date, $time, $location, $waste, $quantity);
        $stmt1->execute();

        // Insert into pickups
        $pickupId = 'PICKUP-' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        $status = 'Pending';
        $stmt2 = $conn->prepare("INSERT INTO pickups (pickup_id, location, status) VALUES (?, ?, ?)");
        $stmt2->bind_param("sss", $pickupId, $location, $status);
        $stmt2->execute();

        // Trigger SMS
        include 'test.php';

        header("Location: thankyou.html");
        exit();
    } else {
        echo "<script>alert('All fields are required.');</script>";
    }
}
?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pickup Request</title>
  <link rel="stylesheet" href="request.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Hanuman&family=Instrument+Sans:wght@400;500;600;700&family=Istok+Web&display=swap" rel="stylesheet"/>

  <!-- Leaflet map CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
</head>
<body>

<div class="request">
  <header class="header">
    <div class="logo-container">
      <img src="https://cdn.builder.io/api/v1/image/assets/39e484a40a9f4a41847915213f7e6770/75865e3ae523461c58906f34e844609394b4984f?placeholderIfAbsent=true" alt="Bin-Buddy Logo" class="logo-image" />
      <div class="brand-info">
        <h1 class="brand-name">THE BIN-BUDDY</h1>
      </div>
    </div>
    <?php include 'nav.php'; ?>
  </header>

  <main class="form-container">
    <h2 class="form-title">Request For Pickup</h2>
    <form id="pickupForm" action="request.php" method="POST">
      <section class="date-time-section">
        <div class="date-container">
          <label for="date" class="field-label">Date</label>
          <input type="date" id="date" name="date" required class="date-input"/>
        </div>
        <div class="time-container">
          <label for="time" class="field-label">Time</label>
          <input type="time" id="time" name="time" required class="time-input"/>
        </div>
      </section>

      <section class="location-section">
        <label for="location" class="field-label">Enter Location Details</label>
        <input type="text" id="addressInput" name="location" placeholder="Click on map to autofill" required class="address-input"/>
        <div id="map" style="height: 300px; border-radius: 8px;"></div>
        </section>

      <section class="waste-category-section">
        <h3 class="field-label">Select Waste Category</h3>
        <div class="waste-options">
          <div class="waste-options-row">
            <label class="waste-option">
              <input type="radio" name="waste" value="Biodegradable" required/>
              Biodegradable
            </label>
            <label class="waste-option">
              <input type="radio" name="waste" value="Non-Biodegradable"/>
              Non-Biodegradable
            </label>
          </div>
          <label class="waste-option recyclable-option">
            <input type="radio" name="waste" value="Recyclable"/>
            Recyclable
          </label>
        </div>
      </section>

      <section class="quantity-section">
        <label for="quantity" class="field-label">Quantity (in kg)</label>
        <input type="number" id="quantity" name="quantity" placeholder="Enter Quantity" class="quantity-input" required min="1"/>
      </section>

      <button type="submit" class="confirm-button">Confirm Request</button>
    </form>
  </main>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
  const map = L.map('map').setView([27.7172, 85.3240], 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  L.Control.geocoder().addTo(map);

  let marker;

  map.on('click', function (e) {
    const { lat, lng } = e.latlng;

    if (!marker) {
      marker = L.marker([lat, lng], { draggable: true }).addTo(map);
      marker.on('dragend', function () {
        const pos = marker.getLatLng();
        reverseGeocode(pos.lat, pos.lng);
      });
    } else {
      marker.setLatLng([lat, lng]);
    }

    reverseGeocode(lat, lng);
  });

  function reverseGeocode(lat, lng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
      .then(res => res.json())
      .then(data => {
        const address = data.display_name || `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
        document.getElementById('addressInput').value = address;
      })
      .catch(() => {
        document.getElementById('addressInput').value = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
      });
  }
</script>

</body>
</html>
