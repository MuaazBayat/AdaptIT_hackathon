<?php
$username = "#####";
$password = "#####";
$database = "#####";
$host = "######";
// Step 1: Connect to DB
$link = mysqli_connect($host, $username, $password, $database);

if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}
// Step 2: Validate and format request parameters
// Check if latitude and longitude were received from the POST request
// Guard 
if (!isset($_POST["Latitude"]) || !isset($_POST["Longitude"])) {
    die("Error: Latitude and Longitude not provided in the POST request.");
}

// Get latitude and longitude from the WhatsApp message
$latitude = $_POST["Latitude"];
$longitude = $_POST["Longitude"];

// Step3: Query the database to get the job listings
$query = "SELECT task_title, description, city, est_payment, city_coordinates FROM tblListings";
$result = mysqli_query($link, $query);
// Guard
if (!$result) {
    die("Query failed: " . mysqli_error($link));
}
// Step 4: Get distances between co-ordinate sets from resultset and user_location
// Function to calculate the distance between two sets of coordinates (haversine)
// see: https://www.igismap.com/haversine-formula-calculate-geographic-distance-earth/
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // in kilometers

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    $distance = $earthRadius * $c;
    return $distance;
}
// Array to store the job listings within the specified distance
$jobsWithinDistance = array();

while ($row = mysqli_fetch_assoc($result)) {
    $listingCoordinates = json_decode($row['city_coordinates'], true);
    $listingLatitude = $listingCoordinates['latitude'];
    $listingLongitude = $listingCoordinates['longitude'];

    $distance = calculateDistance($latitude, $longitude, $listingLatitude, $listingLongitude);

    // Sets the maximum distance to consider a job listing
    $maxDistance = 100; // in kilometers

    if ($distance <= $maxDistance) {
        $row['distance'] = $distance; // Add the distance to the job listing
        $jobsWithinDistance[] = $row;
    }
}

// Step 5: Sort the job listings based on distance (closest first)
usort($jobsWithinDistance, function ($a, $b) {
    return $a['distance'] - $b['distance'];
});

// Step 6: Limit the results to the closest 5 job listings
$closestJobs = array_slice($jobsWithinDistance, 0, 5);

// Step 7: Convert the results to JSON and echo the response
header('Content-Type: application/json');
echo json_encode($closestJobs);

mysqli_close($link);
?>
