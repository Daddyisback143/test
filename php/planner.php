<?php
// php/planner.php - Trip planning functionality
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to_city = $_POST['to_city'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $travelers = $_POST['travelers'] ?? 1;
    $travel_style = $_POST['travel_style'] ?? 'standard';

    if (empty($to_city) || empty($start_date) || empty($end_date)) {
        echo json_encode(['status' => 'error', 'msg' => 'Please fill in all required fields']);
        exit;
    }

    // Calculate trip duration
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $duration = $start->diff($end)->days;

    if ($duration <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'End date must be after start date']);
        exit;
    }

    // City plans data - Expanded database
    $cityPlans = [
        'paris' => [
            'sights' => [
                ['name' => 'Eiffel Tower', 'cost' => 25],
                ['name' => 'Louvre Museum', 'cost' => 17],
                ['name' => 'Notre-Dame Cathedral', 'cost' => 0],
                ['name' => 'Montmartre', 'cost' => 0],
                ['name' => 'Seine River Cruise', 'cost' => 15]
            ],
            'hotel' => 12000,
            'food' => 2500,
            'transport' => 1000
        ],
        'london' => [
            'sights' => [
                ['name' => 'London Eye', 'cost' => 30],
                ['name' => 'British Museum', 'cost' => 0],
                ['name' => 'Tower of London', 'cost' => 25],
                ['name' => 'Buckingham Palace', 'cost' => 0],
                ['name' => 'Westminster Abbey', 'cost' => 20]
            ],
            'hotel' => 11000,
            'food' => 2400,
            'transport' => 900
        ],
        'new_york' => [
            'sights' => [
                ['name' => 'Statue of Liberty', 'cost' => 20],
                ['name' => 'Central Park', 'cost' => 0],
                ['name' => 'Times Square', 'cost' => 0],
                ['name' => 'Empire State Building', 'cost' => 35],
                ['name' => 'Broadway Show', 'cost' => 80]
            ],
            'hotel' => 15000,
            'food' => 3000,
            'transport' => 1200
        ],
        'tokyo' => [
            'sights' => [
                ['name' => 'Senso-ji Temple', 'cost' => 0],
                ['name' => 'Tokyo Skytree', 'cost' => 25],
                ['name' => 'Shibuya Crossing', 'cost' => 0],
                ['name' => 'Tsukiji Fish Market', 'cost' => 0],
                ['name' => 'Mount Fuji Tour', 'cost' => 50]
            ],
            'hotel' => 13000,
            'food' => 2800,
            'transport' => 1100
        ],
        'dubai' => [
            'sights' => [
                ['name' => 'Burj Khalifa', 'cost' => 40],
                ['name' => 'Palm Jumeirah', 'cost' => 0],
                ['name' => 'Dubai Mall', 'cost' => 0],
                ['name' => 'Desert Safari', 'cost' => 60],
                ['name' => 'Dubai Fountain', 'cost' => 0]
            ],
            'hotel' => 14000,
            'food' => 2600,
            'transport' => 1000
        ],
        'singapore' => [
            'sights' => [
                ['name' => 'Marina Bay Sands', 'cost' => 30],
                ['name' => 'Gardens by the Bay', 'cost' => 20],
                ['name' => 'Universal Studios', 'cost' => 70],
                ['name' => 'Sentosa Island', 'cost' => 40],
                ['name' => 'Singapore Zoo', 'cost' => 35]
            ],
            'hotel' => 12000,
            'food' => 2200,
            'transport' => 800
        ],
        'mumbai' => [
            'sights' => [
                ['name' => 'Gateway of India', 'cost' => 0],
                ['name' => 'Marine Drive', 'cost' => 0],
                ['name' => 'Juhu Beach', 'cost' => 0],
                ['name' => 'Elephanta Caves', 'cost' => 10],
                ['name' => 'Bollywood Studio Tour', 'cost' => 25]
            ],
            'hotel' => 4000,
            'food' => 800,
            'transport' => 400
        ],
        'delhi' => [
            'sights' => [
                ['name' => 'Red Fort', 'cost' => 15],
                ['name' => 'Qutub Minar', 'cost' => 10],
                ['name' => 'India Gate', 'cost' => 0],
                ['name' => 'Humayun\'s Tomb', 'cost' => 10],
                ['name' => 'Lotus Temple', 'cost' => 0]
            ],
            'hotel' => 3500,
            'food' => 700,
            'transport' => 350
        ],
        'jaipur' => [
            'sights' => [
                ['name' => 'Amber Fort', 'cost' => 20],
                ['name' => 'City Palace', 'cost' => 15],
                ['name' => 'Hawa Mahal', 'cost' => 10],
                ['name' => 'Jantar Mantar', 'cost' => 10],
                ['name' => 'Nahargarh Fort', 'cost' => 5]
            ],
            'hotel' => 3000,
            'food' => 600,
            'transport' => 300
        ],
        'goa' => [
            'sights' => [
                ['name' => 'Calangute Beach', 'cost' => 0],
                ['name' => 'Baga Beach', 'cost' => 0],
                ['name' => 'Fort Aguada', 'cost' => 25],
                ['name' => 'Basilica of Bom Jesus', 'cost' => 0],
                ['name' => 'Spice Plantation', 'cost' => 800]
            ],
            'hotel' => 3500,
            'food' => 900,
            'transport' => 500
        ],
        'kerala' => [
            'sights' => [
                ['name' => 'Alleppey Backwaters', 'cost' => 0],
                ['name' => 'Munnar Tea Gardens', 'cost' => 0],
                ['name' => 'Kumarakom Bird Sanctuary', 'cost' => 50],
                ['name' => 'Fort Kochi', 'cost' => 0],
                ['name' => 'Varkala Beach', 'cost' => 0]
            ],
            'hotel' => 3000,
            'food' => 800,
            'transport' => 500
        ],
        'udaipur' => [
            'sights' => [
                ['name' => 'Lake Palace', 'cost' => 0],
                ['name' => 'City Palace', 'cost' => 15],
                ['name' => 'Jag Mandir', 'cost' => 10],
                ['name' => 'Sajjangarh Palace', 'cost' => 5],
                ['name' => 'Fateh Sagar Lake', 'cost' => 0]
            ],
            'hotel' => 2800,
            'food' => 600,
            'transport' => 300
        ]
    ];

    // Check if city exists (case insensitive)
    $cityFound = false;
    $actualCityKey = '';
    
    foreach ($cityPlans as $key => $data) {
        if (strtolower($to_city) === strtolower($key)) {
            $cityFound = true;
            $actualCityKey = $key;
            break;
        }
    }

    if (!$cityFound) {
        echo json_encode(['status' => 'error', 'msg' => 'City "' . htmlspecialchars($to_city) . '" not found in our database. Available cities: ' . implode(', ', array_keys($cityPlans))]);
        exit;
    }

    $cityData = $cityPlans[$actualCityKey];
    
    // Calculate costs based on travel style
    $styleMultiplier = [
        'budget' => 0.7,
        'standard' => 1.0,
        'luxury' => 1.5
    ];
    
    $multiplier = $styleMultiplier[$travel_style] ?? 1.0;
    
    // Calculate total costs
    $hotelCost = $cityData['hotel'] * $duration * $multiplier;
    $foodCost = $cityData['food'] * $duration * $travelers;
    $transportCost = $cityData['transport'] * $duration * $multiplier;
    
    $sightsCost = 0;
    foreach ($cityData['sights'] as $sight) {
        $sightsCost += $sight['cost'] * $travelers;
    }
    
    $totalCost = ($hotelCost + $foodCost + $transportCost + $sightsCost) * $travelers;
    
    // Generate HTML for the plan
    $html = "
    <div class='plan-header'>
        <h3>Your Trip to " . ucfirst($actualCityKey) . "</h3>
        <p>Duration: {$duration} days | Travelers: {$travelers} | Style: " . ucfirst($travel_style) . "</p>
    </div>
    <div class='plan-content'>
        <div class='plan-section'>
            <h4>🏨 Accommodation</h4>
            <p>Estimated hotel cost: ₹" . number_format($hotelCost) . "</p>
            <p>Based on {$travel_style} style accommodation</p>
        </div>
        <div class='plan-section'>
            <h4>🍽️ Food & Dining</h4>
            <p>Daily food budget: ₹" . number_format($cityData['food']) . "</p>
            <p>Total food cost: ₹" . number_format($foodCost) . "</p>
        </div>
        <div class='plan-section'>
            <h4>🚗 Transportation</h4>
            <p>Daily transport: ₹" . number_format($cityData['transport']) . "</p>
            <p>Total transport cost: ₹" . number_format($transportCost) . "</p>
        </div>
        <div class='plan-section sights-list'>
            <h4>🎯 Popular Sights</h4>
            <ul>";
    
    foreach ($cityData['sights'] as $sight) {
        $costText = $sight['cost'] > 0 ? " (₹{$sight['cost']})" : " (Free)";
        $html .= "<li>{$sight['name']}{$costText}</li>";
    }
    
    $html .= "</ul>
        </div>
        <div class='plan-section cost-breakdown'>
            <h4>💰 Cost Breakdown</h4>
            <ul>
                <li>Hotel: ₹" . number_format($hotelCost) . "</li>
                <li>Food: ₹" . number_format($foodCost) . "</li>
                <li>Transport: ₹" . number_format($transportCost) . "</li>
                <li>Sights: ₹" . number_format($sightsCost) . "</li>
                <li class='total'>Total: ₹" . number_format($totalCost) . "</li>
            </ul>
        </div>
    </div>
    <div class='plan-actions'>
        <a href='booking.html' class='btn'>Book This Trip</a>
        <a href='packages.html' class='btn btn-download'>View Packages</a>
    </div>";

    echo json_encode([
        'status' => 'success',
        'html' => $html,
        'total_cost' => $totalCost
    ]);

} else {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request method']);
}
?>
