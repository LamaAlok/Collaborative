<?php
require 'db.php'; // DB connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $waste = $_POST['waste'];
    $quantity = $_POST['quantity'];

    if ($date && $time && $location && $waste && $quantity) {
        // Insert into pickup_requests
        $stmt1 = $conn->prepare("INSERT INTO pickup_requests (date, time, location, category, quantity, requested_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt1->bind_param("sssss", $date, $time, $location, $waste, $quantity);
        $stmt1->execute();

        // Generate pickup_id
        $pickupId = 'PICKUP-' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        $status = 'Pending';

        // Insert into pickups
        $stmt2 = $conn->prepare("INSERT INTO pickups (pickup_id, location, status) VALUES (?, ?, ?)");
        $stmt2->bind_param("sss", $pickupId, $location, $status);
        $stmt2->execute();

        // Redirect to thankyou.html
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
  <title>Waste Management Pickup Request</title>
  <link rel="stylesheet" href="request.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Hanuman&family=Instrument+Sans:wght@400;500;600;700&family=Istok+Web&display=swap" rel="stylesheet"/>
  <style>
    #suggestions {
      background: #fff;
      border: 1px solid #ccc;
      max-width: 100%;
      margin-top: 2px;
      position: absolute;
      z-index: 999;
      list-style: none;
      padding: 0;
    }
    #suggestions li {
      padding: 8px 12px;
      cursor: pointer;
    }
    #suggestions li:hover {
      background-color: #f0f0f0;
    }
    .relative-container {
      position: relative;
      width: 100%;
    }
  </style>
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
        <div class="relative-container">
          <input type="text" id="location" name="location" required placeholder="Enter Address" class="address-input" oninput="searchLocation()"/>
          <ul id="suggestions"></ul>
        </div>
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

<script>
  function searchLocation() {
    const input = document.getElementById('location').value;
    const suggestionsList = document.getElementById('suggestions');

    if (input.length < 3) {
      suggestionsList.innerHTML = '';
      return;
    }

    fetch(`https://photon.komoot.io/api/?q=${encodeURIComponent(input)}`)
      .then(response => response.json())
      .then(data => {
        const results = data.features.map(
          feature => {
            const name = feature.properties.name || '';
            const city = feature.properties.city || '';
            const full = `${name}, ${city}`;
            return `<li onclick="selectLocation('${full}')">${full}</li>`;
          }
        ).join('');
        suggestionsList.innerHTML = results;
      })
      .catch(err => {
        console.error('Location error:', err);
        suggestionsList.innerHTML = '';
      });
  }

  function selectLocation(address) {
    document.getElementById('location').value = address;
    document.getElementById('suggestions').innerHTML = '';
  }
</script>

</body>
</html>
