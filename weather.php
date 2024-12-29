<?php
// Get latitude and longitude from query parameters
$latitude = isset($_GET['latitude']) ? $_GET['latitude'] : null;
$longitude = isset($_GET['longitude']) ? $_GET['longitude'] : null;

// If no location is provided, use Shiraz as the default
if ($latitude === null || $longitude === null) {
    $latitude = 29.713583;
    $longitude = 52.455833; // Default coordinates for Shiraz
}

// Fetch the weather data from Open Meteo API
$url = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&current=temperature_2m,weather_code&timezone=auto";

// Get the response
$response = @file_get_contents($url); // Suppress errors with @
if ($response === FALSE) {
    // If unable to fetch data, do nothing (hide output)
    exit; 
}

// Decode the JSON response
$data = json_decode($response, true);

// Check if the data contains current temperature and weather code
if (isset($data['current']['temperature_2m']) && isset($data['current']['weather_code'])) {
    $temperature = $data['current']['temperature_2m'];
    $weather_code = $data['current']['weather_code'];

    // Map the weather code to emoji
    $weather_emojis = [
        0 => '☀️',  // Clear sky
        1 => '🌤️',  // Mainly clear
        2 => '🌥️',  // Partly cloudy
        3 => '☁️',  // Overcast
        45 => '🌫️', // Fog
        48 => '🌫️', // Depositing rime fog
        51 => '🌧️', // Drizzle
        53 => '🌧️', // Rain
        61 => '🌧️', // Showers
        63 => '🌧️', // Rain
        80 => '🌧️', // Rain showers
        95 => '⚡',   // Thunderstorms
        99 => '🌩️'   // Severe thunderstorms
    ];

    // Get the corresponding emoji or default to a cloud emoji
    $emoji = $weather_emojis[$weather_code] ?? '☁️';

    // Determine location name
    $location_name = ($latitude == 29.713583 && $longitude == 52.455833) ? "شیراز" : "دما ";

    // Display the temperature, weather emoji, and location name
    echo "{$location_name}: " . " ° " . $temperature . "  " . $emoji;
} else {
    // If data is not available, do nothing (hide output)
    exit; 
}
?>