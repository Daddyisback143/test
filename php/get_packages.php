<?php
// php/get_packages.php - Serves package data as JSON
header('Content-Type: application/json');

// Sample package data (in a real app, this would come from a database)
$domesticPackages = [
    [
        'id' => 1,
        'name' => 'Kerala Backwaters',
        'duration' => '5 days',
        'cities' => ['Kochi', 'Alleppey', 'Munnar'],
        'price_per_person' => 25000,
        'meals' => 'Breakfast & Dinner',
        'hotels' => '3-star',
        'travel_mode' => 'Flight + Cab',
        'image' => 'https://source.unsplash.com/featured/?kerala',
        'description' => 'Experience the serene backwaters of Kerala, stay in a houseboat, and explore the beautiful hill stations.'
    ],
    [
        'id' => 2,
        'name' => 'Rajasthan Heritage',
        'duration' => '7 days',
        'cities' => ['Jaipur', 'Udaipur', 'Jodhpur'],
        'price_per_person' => 35000,
        'meals' => 'All Meals',
        'hotels' => '4-star',
        'travel_mode' => 'Flight + Cab',
        'image' => 'https://source.unsplash.com/featured/?rajasthan',
        'description' => 'Explore the royal heritage of Rajasthan, visit magnificent palaces and forts.'
    ],
    [
        'id' => 3,
        'name' => 'Goa Beach Holiday',
        'duration' => '4 days',
        'cities' => ['North Goa', 'South Goa'],
        'price_per_person' => 20000,
        'meals' => 'Breakfast',
        'hotels' => '3-star',
        'travel_mode' => 'Flight',
        'image' => 'https://source.unsplash.com/featured/?goa',
        'description' => 'Enjoy the beaches, water sports, and vibrant nightlife of Goa.'
    ]
];

$internationalPackages = [
    [
        'id' => 4,
        'name' => 'Maldives Paradise',
        'duration' => '5 days',
        'cities' => ['Male', 'Maafushi'],
        'price_per_person' => 80000,
        'meals' => 'All Meals',
        'hotels' => '4-star',
        'travel_mode' => 'Flight + Speedboat',
        'image' => 'https://source.unsplash.com/featured/?maldives',
        'description' => 'Experience luxury in overwater villas, enjoy water sports and spa treatments.'
    ],
    [
        'id' => 5,
        'name' => 'Swiss Alps Adventure',
        'duration' => '7 days',
        'cities' => ['Zurich', 'Interlaken', 'Zermatt'],
        'price_per_person' => 150000,
        'meals' => 'Breakfast & Dinner',
        'hotels' => '4-star',
        'travel_mode' => 'Flight + Train',
        'image' => 'https://source.unsplash.com/featured/?swiss-alps',
        'description' => 'Explore the majestic Swiss Alps, enjoy scenic train rides and mountain activities.'
    ],
    [
        'id' => 6,
        'name' => 'Thailand Explorer',
        'duration' => '6 days',
        'cities' => ['Bangkok', 'Phuket'],
        'price_per_person' => 45000,
        'meals' => 'Breakfast & Dinner',
        'hotels' => '4-star',
        'travel_mode' => 'Flight',
        'image' => 'https://source.unsplash.com/featured/?thailand',
        'description' => 'Experience the vibrant culture, beautiful beaches, and delicious cuisine of Thailand.'
    ]
];

// If a city is specified, filter packages by that city
if (isset($_GET['city'])) {
    $city = strtolower($_GET['city']);
    $filteredDomestic = array_filter($domesticPackages, function($pkg) use ($city) {
        return in_array($city, array_map('strtolower', $pkg['cities']));
    });
    $filteredInternational = array_filter($internationalPackages, function($pkg) use ($city) {
        return in_array($city, array_map('strtolower', $pkg['cities']));
    });
    $response = [
        'domestic' => array_values($filteredDomestic),
        'international' => array_values($filteredInternational)
    ];
} else {
    $response = [
        'domestic' => $domesticPackages,
        'international' => $internationalPackages
    ];
}

// Return the package data as JSON
echo json_encode($response); 