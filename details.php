<?php
$type = $_GET['type'] ?? '';
$city = $_GET['city'] ?? '';
$name = $_GET['name'] ?? '';
$img = $_GET['img'] ?? '';

function getCityDetails($city) {
  $plans = [
    'maldives' => [
      'title' => 'Maldives',
      'desc' => 'Crystal clear waters and sandy beaches.',
      'img' => 'https://th.bing.com/th/id/OIP.sqTxI06FYbaTuyn2drdj8wHaEJ?rs=1&pid=ImgDetMain',
      'sights' => ['Maafushi Island', 'Banana Reef', 'Male City Tour', 'Hulhumale Beach'],
      'packages' => [
        ['name' => 'Maldives Explorer', 'details' => '4 days, 3 nights. Maafushi, Banana Reef, city tour.', 'price' => 24999],
        ['name' => 'Beach Relax', 'details' => '3 days, 2 nights. Hulhumale, water sports, spa.', 'price' => 19999]
      ]
    ],
    'swiss alps' => [
      'title' => 'Swiss Alps',
      'desc' => 'Snowy peaks and scenic views.',
      'img' => 'https://th.bing.com/th/id/OIP.3gP11prJZHJPoktQ48n34AHaE8?w=265&h=180&c=7&r=0&o=5&dpr=1.3&pid=1.7',
      'sights' => ['Jungfraujoch', 'Matterhorn', 'Lake Lucerne', 'Interlaken'],
      'packages' => [
        ['name' => 'Alps Adventure', 'details' => '5 days, 4 nights. Jungfraujoch, Matterhorn, Lucerne.', 'price' => 49999],
        ['name' => 'Swiss Scenic', 'details' => '4 days, 3 nights. Interlaken, lakes, mountain train.', 'price' => 39999]
      ]
    ],
    'new york' => [
      'title' => 'New York',
      'desc' => 'The city that never sleeps.',
      'img' => 'https://wallpaperaccess.com/full/1211839.jpg',
      'sights' => ['Statue of Liberty', 'Central Park', 'Empire State Building', 'Times Square'],
      'packages' => [
        ['name' => 'NYC Highlights', 'details' => '4 days, 3 nights. Statue of Liberty, Empire State, Times Square.', 'price' => 45999],
        ['name' => 'Big Apple Tour', 'details' => '3 days, 2 nights. Central Park, shopping, Broadway.', 'price' => 39999]
      ]
    ],
    'amazon' => [
      'title' => 'Amazon',
      'desc' => 'Lush rainforests and exotic wildlife.',
      'img' => 'https://images.rawpixel.com/image_800/cHJpdmF0ZS9zci9pbWFnZXMvd2Vic2l0ZS8yMDIzLTExL3Jhd3BpeGVsX29mZmljZV8yM19hX3BpY3R1cmVfb2ZfYV9hbWF6b25fZm9yZXN0X2xhbmRzY2FwZV93aXRoX19kMThmMTdkNy0xNjlkLTQzZTctODhiYS0yM2RkZDJhZmY4ZGNfMS5qcGc.jpg',
      'sights' => ['Manaus City', 'Amazon Rainforest Tour', 'Meeting of Waters', 'Anavilhanas National Park'],
      'packages' => [
        ['name' => 'Amazon Explorer', 'details' => '5 days, 4 nights. Rainforest, river cruise, wildlife.', 'price' => 34999],
        ['name' => 'Jungle Adventure', 'details' => '4 days, 3 nights. National Park, Manaus, eco-lodge.', 'price' => 29999]
      ]
    ]
  ];
  return $plans[$city] ?? null;
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Details</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body { font-family: 'Poppins', sans-serif; background: #f7f7f7; margin: 0; }
    .details-container { max-width: 600px; margin: 2em auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px #0001; padding: 2em; }
    .details-container img { width: 100%; border-radius: 10px; margin-bottom: 1em; }
    .details-container h2 { margin-top: 0; }
    .package-card { background: #f0f8ff; border-radius: 8px; margin: 1em 0; padding: 1em; }
    .gallery-img { width: 100%; border-radius: 10px; }
    .back-btn { display: inline-block; margin-top: 1em; padding: 0.5em 1.5em; background: #007bff; color: #fff; border-radius: 6px; text-decoration: none; }
    .btn { padding: 0.7em 1.2em; background: #28a745; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
    .btn:hover { background: #218838; }
    .auth {
      background: linear-gradient(120deg, #6dd5ed 0%, #2193b0 100%);
      padding: 2em 0 3em 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .auth .container {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 32px #0002;
      padding: 2.5em 2em 2em 2em;
      max-width: 400px;
      width: 100%;
      margin: 0 auto;
    }
    .auth-form input {
      width: 100%;
      padding: 0.8em 1em;
      margin: 0.7em 0;
      border: 1px solid #b2d8e6;
      border-radius: 8px;
      font-size: 1em;
      background: #f7fbfc;
      transition: border 0.2s;
    }
    .auth-form input:focus {
      border: 1.5px solid #2193b0;
      outline: none;
      background: #eaf6fb;
    }
    .auth-form button.btn {
      width: 100%;
      background: linear-gradient(90deg, #2193b0 0%, #6dd5ed 100%);
      color: #fff;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      padding: 0.9em 0;
      margin-top: 1em;
      font-size: 1.1em;
      cursor: pointer;
      box-shadow: 0 2px 8px #2193b033;
      transition: background 0.2s;
    }
    .auth-form button.btn:hover {
      background: linear-gradient(90deg, #6dd5ed 0%, #2193b0 100%);
    }
    .auth-form p {
      margin: 1em 0 0 0;
      text-align: center;
      color: #2193b0;
    }
    #login-result .success, #register-result .success {
      color: #28a745;
      background: #eafaf1;
      border-radius: 6px;
      padding: 0.7em 1em;
      margin-top: 1em;
      text-align: center;
    }
    #login-result .error, #register-result .error {
      color: #c0392b;
      background: #fdecea;
      border-radius: 6px;
      padding: 0.7em 1em;
      margin-top: 1em;
      text-align: center;
    }
    .fare-details-box { background: #e3f2fd; border-radius: 12px; box-shadow: 0 2px 12px #2193b033; padding: 2em; margin: 2em 0; }
    .fare-details-box h3 { color: #0077cc; }
    .traveller-form input, .traveller-form select { margin: 0.5em 0; padding: 0.5em; border-radius: 6px; border: 1px solid #b2d8e6; width: 90%; }
    .traveller-form label { display: block; margin-top: 1em; }
    .traveller-form .btn { margin-top: 1em; }
  </style>
</head>
<body>
<div class="details-container">
<?php if ($type === 'destination' && $city):
  $details = getCityDetails($city);
  if ($details): ?>
    <img src="<?= htmlspecialchars($details['img']) ?>" alt="<?= htmlspecialchars($details['title']) ?>">
    <h2><?= htmlspecialchars($details['title']) ?></h2>
    <p><?= htmlspecialchars($details['desc']) ?></p>
    <h3>Sights</h3>
    <ul>
      <?php foreach ($details['sights'] as $sight): ?>
        <li><?= htmlspecialchars($sight) ?></li>
      <?php endforeach; ?>
    </ul>
    <h3>Packages</h3>
    <?php foreach ($details['packages'] as $pkg): ?>
      <div class="package-card">
        <h4><?= htmlspecialchars($pkg['name']) ?></h4>
        <p><?= htmlspecialchars($pkg['details']) ?></p>
        <div><b>Price:</b> ₹<?= number_format($pkg['price']) ?></div>
        <button class="btn select-fare-btn" data-pkg='<?= json_encode($pkg, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>Select &amp; Book</button>
      </div>
    <?php endforeach; ?>
    <div id="fare-booking-section" style="display:none;"></div>
    <script>
      // Fare selection logic
      document.querySelectorAll('.select-fare-btn').forEach(btn => {
        btn.onclick = function() {
          // Hide all package cards and show only booking section
          document.querySelectorAll('.package-card').forEach(card => card.style.display = 'none');
          document.getElementById('fare-booking-section').style.display = 'block';
          const pkg = JSON.parse(this.getAttribute('data-pkg'));
          document.getElementById('fare-booking-section').innerHTML = `
            <div class="fare-details-box">
              <h3>Booking: ${pkg.name}</h3>
              <div><b>Details:</b> ${pkg.details}</div>
              <div><b>Price:</b> ₹${pkg.price}</div>
              <form id="fare-booking-form" class="traveller-form" method="post" action="php/book.php" style="margin-top:1.5em;">
                <input type="hidden" name="type" value="destination">
                <input type="hidden" name="destination_name" value="${pkg.name}">
                <label>Travel Date: <input type="date" name="travel_date" required></label><br><br>
                <label>Number of Persons: <input type="number" name="num_persons" id="fare-num-persons" min="1" max="20" required></label><br><br>
                <label>Phone Number: <input type="tel" name="phone" required></label><br><br>
                <div id="fare-traveler-details"></div>
                <button type="button" id="add-fare-traveler" class="btn">Add Traveler Details</button><br><br>
                <button type="submit" class="btn">Confirm & Download PDF</button>
              </form>
            </div>
          `;
          // Add CSS for the fare details box
          const style = document.createElement('style');
          style.innerHTML = `.fare-details-box { background: #e3f2fd; border-radius: 12px; box-shadow: 0 2px 12px #2193b033; padding: 2em; margin: 2em 0; }
            .fare-details-box h3 { color: #0077cc; }
            .traveller-form input, .traveller-form select { margin: 0.5em 0; padding: 0.5em; border-radius: 6px; border: 1px solid #b2d8e6; width: 90%; }
            .traveller-form label { display: block; margin-top: 1em; }
            .traveller-form .btn { margin-top: 1em; }`;
          document.head.appendChild(style);
          // JS for traveler fields
          let fareTravelerCount = 0;
          const fareNumPersonsInput = document.getElementById('fare-num-persons');
          const fareTravelerDetailsDiv = document.getElementById('fare-traveler-details');
          const addFareTravelerBtn = document.getElementById('add-fare-traveler');
          function addFareTravelerField() {
            fareTravelerCount++;
            const div = document.createElement('div');
            div.innerHTML = `<b>Traveler ${fareTravelerCount}:</b> Name: <input name="traveler_name[]" required> Age: <input name="age[]" type="number" min="0" required> Gender: <select name="gender[]"><option>Male</option><option>Female</option><option>Other</option></select><br><br>`;
            fareTravelerDetailsDiv.appendChild(div);
          }
          addFareTravelerBtn.onclick = addFareTravelerField;
          fareNumPersonsInput.addEventListener('change', function() {
            fareTravelerDetailsDiv.innerHTML = '';
            fareTravelerCount = 0;
            const n = parseInt(fareNumPersonsInput.value) || 0;
            for (let i = 0; i < n; i++) addFareTravelerField();
          });
        };
      });
    </script>
<?php else: ?>
    <p>Destination details not found.</p>
<?php endif; ?>
<?php elseif ($type === 'package' && $name): ?>
<?php
// Load all packages (merge domestic and international from JS data, or define here)
$allPackages = [
  // --- Domestic Packages ---
  [
    "name" => "Goa Beach Getaway",
    "days" => 5,
    "nights" => 4,
    "cities" => ["Panaji", "Calangute", "Baga"],
    "price_per_person" => 18999,
    "meals" => "Included",
    "hotels" => ["Taj Vivanta", "Beach Bay Resort"],
    "travel_modes" => ["Flight", "Cab"],
    "image" => "https://media.istockphoto.com/id/157579910/photo/the-beach.jpg?s=612x612&w=0&k=20&c=aMk67AmzIVD_S1Nibww8ytUdyub2ck3HNQ3uTvuPWPI=",
    "description" => "5 days, 4 nights. Explore North & South Goa beaches, nightlife, and spice plantation tour."
  ],
  [
    "name" => "Himachal Adventure Escape",
    "days" => 7,
    "nights" => 6,
    "cities" => ["Shimla", "Manali", "Solang Valley"],
    "price_per_person" => 21999,
    "meals" => "Included",
    "hotels" => ["Snow Valley Resort", "Apple Country Resort"],
    "travel_modes" => ["Train", "Bus", "Cab"],
    "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTTeyWGGWqyUwuDE4tw2HBRyoH7aBldHGk8JQ&s",
    "description" => "7 days, 6 nights. Enjoy snow activities, scenic valleys, and local sightseeing."
  ],
  [
    "name" => "Kerala Backwaters Retreat",
    "days" => 6,
    "nights" => 5,
    "cities" => ["Kochi", "Munnar", "Alleppey"],
    "price_per_person" => 20499,
    "meals" => "Included",
    "hotels" => ["Spice Jungle Resort", "Houseboat Stay"],
    "travel_modes" => ["Flight", "Cab", "Boat"],
    "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTHrnyvcXzeXzyAeGYn9eWncSPM7G_st8yfRg&s",
    "description" => "6 days, 5 nights. Tea gardens, wildlife safari, houseboat cruise through backwaters."
  ],
  [
    "name" => "Golden Triangle Tour",
    "days" => 6,
    "nights" => 5,
    "cities" => ["Delhi", "Agra", "Jaipur"],
    "price_per_person" => 17999,
    "meals" => "Included",
    "hotels" => ["Radisson Blu", "Hotel Clarks Shiraz"],
    "travel_modes" => ["Train", "Cab"],
    "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQSn00QIIVRkAOhOBvypNAdU4ggEx930HARjA&s",
    "description" => "6 days, 5 nights. Visit Taj Mahal, Jaipur forts, and heritage sites."
  ],
  [
    "name" => "Kashmir Paradise Package",
    "days" => 5,
    "nights" => 4,
    "cities" => ["Srinagar", "Gulmarg", "Pahalgam"],
    "price_per_person" => 22999,
    "meals" => "Included",
    "hotels" => ["Heevan Resort", "Khyber Himalayan Resort"],
    "travel_modes" => ["Flight", "Cab"],
    "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQVdZK6HqqNWTBv_-K7dYvOGmVFeGvt2WYfOQ&s",
    "description" => "5 days, 4 nights. Enjoy snow, gardens, Dal Lake shikara ride and sightseeing."
  ],
  [
    "name" => "Andaman Island Escape",
    "days" => 6,
    "nights" => 5,
    "cities" => ["Port Blair", "Havelock Island", "Neil Island"],
    "price_per_person" => 25499,
    "meals" => "Included",
    "hotels" => ["Coral Reef Resort", "Symphony Palms Beach"],
    "travel_modes" => ["Flight", "Boat"],
    "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQauB4DKO11HXk5wxJbeZ3w3jFh-fdZnZuKew&s",
    "description" => "6 days, 5 nights. Crystal-clear waters, scuba diving and beach vibes."
  ],
  [
    "name" => "Rajasthan Royal Trail",
    "days" => 7,
    "nights" => 6,
    "cities" => ["Udaipur", "Jodhpur", "Jaisalmer"],
    "price_per_person" => 20999,
    "meals" => "Included",
    "hotels" => ["Taj Lake Palace", "Desert Camp"],
    "travel_modes" => ["Train", "Cab"],
    "image" => "https://www.tripplannersindia.com/assets/images/page/6_Nights_7_Days_Royal_Rajasthan_Tour_Package.jpg",
    "description" => "7 days, 6 nights. Forts, palaces, desert safari, and culture of Rajasthan."
  ],
  [
    "name" => "Meghalaya & Assam Wonders",
    "days" => 6,
    "nights" => 5,
    "cities" => ["Shillong", "Cherrapunji", "Kaziranga"],
    "price_per_person" => 21999,
    "meals" => "Included",
    "hotels" => ["Ri Kynjai Resort", "IORA Retreat"],
    "travel_modes" => ["Flight", "Cab"],
    "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQLEq4-MVGvGWmWOjDLyv9SRhRWB053Sus62w&s",
    "description" => "6 days, 5 nights. Waterfalls, caves, root bridges and rhino safari."
  ],
  [
    "name" => "Sikkim Scenic Escape",
    "days" => 5,
    "nights" => 4,
    "cities" => ["Gangtok", "Tsomgo Lake", "Nathula Pass"],
    "price_per_person" => 19999,
    "meals" => "Included",
    "hotels" => ["Lemon Tree Hotel", "The Royal Plaza"],
    "travel_modes" => ["Flight", "Cab"],
    "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQMajMFaMMYBcDLdlL2_xoDqn6E4otZp1p8uA&s",
    "description" => "5 days, 4 nights. Mountain views, monasteries, and Indo-China border."
  ],
  [
    "name" => "Trekking in Ladakh",
    "days" => 8,
    "nights" => 7,
    "cities" => ["Leh", "Nubra Valley", "Pangong Lake"],
    "price_per_person" => 29999,
    "meals" => "Included",
    "hotels" => ["Hotel Ladakh Residency", "Campsite at Pangong"],
    "travel_modes" => ["Flight", "Cab"],
    "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQbcQYDs10I2Yahp0YNzuIw8iyolWhpEvrFeQ&s",
    "description" => "8 days, 7 nights. High-altitude desert adventure, monasteries, and lake stays."
  ],
  // --- International Packages ---
  [
    "name" => "Maldives Explorer",
    "days" => 4,
    "nights" => 3,
    "cities" => ["Malé", "Maafushi", "Banana Reef"],
    "price_per_person" => 24999,
    "meals" => "Included",
    "hotels" => ["Paradise Island Resort", "Maafushi Inn"],
    "travel_modes" => ["Flight", "Boat"],
    "image" => "https://th.bing.com/th/id/OIP.7JulycclRJoDpx0FsDdE9wHaEK?w=321&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    "description" => "4 days, 3 nights. Maafushi, Banana Reef, city tour."
  ],
  [
    "name" => "Thailand Delight",
    "days" => 5,
    "nights" => 4,
    "cities" => ["Bangkok", "Pattaya"],
    "price_per_person" => 28999,
    "meals" => "Included",
    "hotels" => ["The Berkeley Hotel", "Avani Pattaya"],
    "travel_modes" => ["Flight", "Cab"],
    "image" => "https://tse1.mm.bing.net/th/id/OIP.XmqaNqgqbbW9yJU2WfjbSgHaEo?r=0&rs=1&pid=ImgDetMain",
    "description" => "5 days, 4 nights. Coral island tour, shopping, nightlife."
  ],
  [
    "name" => "Dubai Luxury Tour",
    "days" => 4,
    "nights" => 3,
    "cities" => ["Dubai", "Abu Dhabi"],
    "price_per_person" => 37999,
    "meals" => "Included",
    "hotels" => ["Burj Al Arab View Hotel", "City Max"],
    "travel_modes" => ["Flight", "Cab"],
    "image" => "https://th.bing.com/th/id/OIP.nugEpuXjt5l18PBRAsHwSgHaEK?w=321&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    "description" => "4 days, 3 nights. Burj Khalifa, desert safari, city tour."
  ],
  [
    "name" => "Singapore City Fun",
    "days" => 5,
    "nights" => 4,
    "cities" => ["Singapore"],
    "price_per_person" => 40999,
    "meals" => "Included",
    "hotels" => ["Marina Bay Sands", "Hotel Boss"],
    "travel_modes" => ["Flight", "Cab", "Metro"],
    "image" => "https://th.bing.com/th/id/OIP.wZHCD6w-OQIJ-qaEj9pghwHaE8?w=274&h=183&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    "description" => "5 days, 4 nights. Gardens by the Bay, Universal Studios, city tour."
  ],
  [
    "name" => "Bali Romantic Escape",
    "days" => 6,
    "nights" => 5,
    "cities" => ["Ubud", "Seminyak"],
    "price_per_person" => 35999,
    "meals" => "Included",
    "hotels" => ["Komaneka Resort", "W Hotel Seminyak"],
    "travel_modes" => ["Flight", "Cab"],
    "image" => "https://th.bing.com/th/id/OIP.e4fy_H5A-PP3qEiB1_DsywHaE8?w=282&h=188&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    "description" => "6 days, 5 nights. Temples, beaches, rice terraces and spa experiences."
  ],
  [
    "name" => "Paris and Swiss Alps Tour",
    "days" => 7,
    "nights" => 6,
    "cities" => ["Paris", "Lucerne", "Interlaken"],
    "price_per_person" => 99999,
    "meals" => "Included",
    "hotels" => ["Ibis Paris", "Hotel Alpina"],
    "travel_modes" => ["Flight", "Train"],
    "image" => "https://th.bing.com/th/id/OIP.ICiz94DwJm5ioU5YbWBvKQHaEK?w=277&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    "description" => "7 days, 6 nights. Eiffel Tower, Mt. Titlis, Jungfrau and boat rides."
  ],
  [
    "name" => "Japan Explorer",
    "days" => 8,
    "nights" => 7,
    "cities" => ["Tokyo", "Kyoto", "Osaka"],
    "price_per_person" => 119999,
    "meals" => "Included",
    "hotels" => ["Shinjuku Granbell", "Kyoto Tower Hotel"],
    "travel_modes" => ["Flight", "Bullet Train"],
    "image" => "https://th.bing.com/th/id/OIP.vWa74pGyfniF_022-LGbFAHaE8?w=228&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    "description" => "8 days, 7 nights. Temples, Mt. Fuji, cherry blossoms, and bullet train ride."
  ],
  [
    "name" => "Turkey Heritage Tour",
    "days" => 6,
    "nights" => 5,
    "cities" => ["Istanbul", "Cappadocia", "Pamukkale"],
    "price_per_person" => 74999,
    "meals" => "Included",
    "hotels" => ["Sultanahmet Hotel", "Cave Suite Hotel"],
    "travel_modes" => ["Flight", "Bus"],
    "image" => "https://th.bing.com/th/id/OIP.pEnCD9C6CbLSrY-A_pv3NwHaE8?w=257&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    "description" => "6 days, 5 nights. Hot air balloon ride, Blue Mosque, thermal springs."
  ],
  [
    "name" => "New Zealand Adventure",
    "days" => 9,
    "nights" => 8,
    "cities" => ["Auckland", "Queenstown", "Rotorua"],
    "price_per_person" => 149999,
    "meals" => "Included",
    "hotels" => ["Novotel", "SkyCity Hotel"],
    "travel_modes" => ["Flight", "Cab", "Train"],
    "image" => "https://th.bing.com/th/id/OIP.cZvZqWgyk5YslMXb9ZMAHQHaE8?w=239&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    "description" => "9 days, 8 nights. Adventure sports, Maori culture, and natural wonders."
  ],
  [
    "name" => "USA East Coast Highlights",
    "days" => 10,
    "nights" => 9,
    "cities" => ["New York", "Washington D.C.", "Niagara Falls"],
    "price_per_person" => 159999,
    "meals" => "Included",
    "hotels" => ["Marriott Marquis", "Embassy Suites"],
    "travel_modes" => ["Flight", "Bus"],
    "image" => "https://th.bing.com/th/id/OIP.a6TlqRxGYLX1BFn9pNVRLAHaEK?w=318&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3",
    "description" => "10 days, 9 nights. Statue of Liberty, Capitol Hill, Niagara boat cruise."
  ]
];
$pkg = null;
foreach ($allPackages as $p) {
  if (strtolower($p['name']) === strtolower($name)) {
    $pkg = $p;
    break;
  }
}
?>
<?php if ($pkg): ?>
  <img src="<?= htmlspecialchars($pkg['image']) ?>" alt="<?= htmlspecialchars($pkg['name']) ?>">
  <h2><?= htmlspecialchars($pkg['name']) ?></h2>
  <div><b>Duration:</b> <?= $pkg['days'] ?> Days / <?= $pkg['nights'] ?> Nights</div>
  <div><b>Cities:</b> <?= htmlspecialchars(implode(', ', $pkg['cities'])) ?></div>
  <div><b>Travel Modes:</b> <?= htmlspecialchars(implode(', ', $pkg['travel_modes'])) ?></div>
  <div><b>Meals:</b> <?= htmlspecialchars($pkg['meals']) ?></div>
  <div><b>Hotels:</b> <?= htmlspecialchars(implode(', ', $pkg['hotels'])) ?></div>
  <div><b>Price per Person:</b> ₹<?= number_format($pkg['price_per_person']) ?></div>
  <div style="margin:1em 0;"><?= htmlspecialchars($pkg['description']) ?></div>
  <button id="book-btn" class="btn">Book This Package</button>
  <form id="booking-form" style="display:none;margin-top:2em;" method="post" action="php/book.php">
    <input type="hidden" name="package_name" value="<?= htmlspecialchars($pkg['name']) ?>">
    <label>Travel Date: <input type="date" name="travel_date" required></label><br><br>
    <label>Number of Persons: <input type="number" name="num_persons" id="pkg-num-persons" min="1" max="20" required></label><br><br>
    <label>Phone Number: <input type="tel" name="phone" required></label><br><br>
    <div id="traveler-details"></div>
    <button type="button" id="add-traveler" class="btn">Add Traveler Details</button><br><br>
    <button type="submit" class="btn">Confirm & Download PDF</button>
    <div id="booking-result"></div>
  </form>
  <script>
    const bookBtn = document.getElementById('book-btn');
    const bookingForm = document.getElementById('booking-form');
    const addTravelerBtn = document.getElementById('add-traveler');
    const travelerDetailsDiv = document.getElementById('traveler-details');
    const pkgNumPersonsInput = document.getElementById('pkg-num-persons');
    let travelerCount = 0;
    function addTravelerField() {
      travelerCount++;
      const div = document.createElement('div');
      div.className = 'traveler-group';
      div.innerHTML = `<h4>Traveler ${travelerCount}</h4><label>Name:</label><input name="name[]" required><label>Age:</label><input name="age[]" type="number" min="1" required><label>Gender:</label><select name="gender[]" required><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>`;
      travelerDetailsDiv.appendChild(div);
    }
    addTravelerBtn.onclick = addTravelerField;
    pkgNumPersonsInput.addEventListener('change', function() {
      travelerDetailsDiv.innerHTML = '';
      travelerCount = 0;
      const n = parseInt(pkgNumPersonsInput.value) || 0;
      for (let i = 0; i < n; i++) addTravelerField();
    });
    // Auto-add fields for default value
    if (pkgNumPersonsInput.value) {
      for (let i = 0; i < parseInt(pkgNumPersonsInput.value); i++) addTravelerField();
    }
    bookingForm.addEventListener('submit', function(ev) {
      ev.preventDefault();
      const formData = new FormData(bookingForm);
      fetch('php/book.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        let html = '';
        if (data.status === 'success') {
          html = `<div class='success'>${data.msg}</div>`;
          if (data.pdf) {
            const blob = new Blob([Uint8Array.from(atob(data.pdf), c => c.charCodeAt(0))], {type: 'application/pdf'});
            const url = URL.createObjectURL(blob);
            html += `<a href='${url}' download='ticket.pdf' class='btn' style='margin-top:1em;'>Download Ticket (PDF)</a>`;
          }
          if (data.html) {
            const htmlTicket = atob(data.html);
            html += `<div style='margin-top:2em;'><b>View Ticket:</b><br><div id='ticket-html-view'></div></div>`;
            setTimeout(() => {
              document.getElementById('ticket-html-view').innerHTML = htmlTicket;
              if (data.ticket_no || data.transport_no) {
                const extra = document.createElement('div');
                extra.style.marginTop = '1em';
                extra.innerHTML =
                  (data.ticket_no ? `<b>Ticket No:</b> ${data.ticket_no}<br>` : '') +
                  (data.transport_no ? `<b>${data.transport_no}</b><br>` : '');
                document.getElementById('ticket-html-view').appendChild(extra);
              }
            }, 0);
          }
        } else {
          html = `<div class='error'>${data.msg || 'Booking failed.'}</div>`;
        }
        document.getElementById('booking-result').innerHTML = html;
      })
      .catch(() => {
        document.getElementById('booking-result').innerHTML = `<div class='error'>Booking failed. Please try again.</div>`;
      });
    });
  </script>
<?php else: ?>
  <p>Package details not found.</p>
<?php endif; ?>
<?php elseif ($type === 'gallery' && $img): ?>
    <img src="<?= htmlspecialchars($img) ?>" class="gallery-img" alt="Gallery Image">
    <h2>Photo Gallery</h2>
    <p>Enjoy this beautiful travel photo!</p>
<?php else: ?>
    <p>No details to show.</p>
<?php endif; ?>
  <a href="javascript:window.close()" class="back-btn">Close Window</a>
</div>
</body>
</html>
