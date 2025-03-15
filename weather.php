<?php
header("Content-Type: application/json");

// Database connection
$host = "localhost";
$dbname = "weather_db";
$user = "root";
$password = "";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

if (!isset($_GET["city"])) {
    echo json_encode(["error" => "City is required"]);
    exit;
}

$city = $conn->real_escape_string($_GET["city"]);
$apiKey = "3dec1de0c97532a73434b0d37116e9b0";
$apiUrl = "https://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey&units=metric";

$weatherData = file_get_contents($apiUrl);
$data = json_decode($weatherData, true);

if ($data["cod"] != 200) {
    echo json_encode(["error" => "City not found"]);
    exit;
}

// Extract weather details
$temperature = $data["main"]["temp"];
$humidity = $data["main"]["humidity"];
$condition = $data["weather"][0]["description"];
$wind_speed = $data["wind"]["speed"];
$country = $data["sys"]["country"];
$latitude = $data["coord"]["lat"];
$longitude = $data["coord"]["lon"];
$pressure = $data["main"]["pressure"];

// Check if location exists in MySQL
$sql = "SELECT id FROM locations WHERE city_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $city);
$stmt->execute();
$result = $stmt->get_result();
$location = $result->fetch_assoc();

if (!$location) {
    // Insert new location
    $sql = "INSERT INTO locations (city_name, country, latitude, longitude) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdd", $city, $country, $latitude, $longitude);
    $stmt->execute();
    $location_id = $stmt->insert_id;
} else {
    $location_id = $location["id"];
}

// Insert weather data
$sql = "INSERT INTO weather_data (location_id, temperature, humidity, condition_text, wind_speed) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("idisd", $location_id, $temperature, $humidity, $condition, $wind_speed);
$stmt->execute();

// Return weather data as JSON
echo json_encode([
    "city" => "$city, $country",
    "temperature" => $temperature,
    "humidity" => $humidity,
    "condition" => $condition,
    "wind_speed" => $wind_speed,
    "pressure" => $pressure
]);

$conn->close();
?>
