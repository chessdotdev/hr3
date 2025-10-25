<?php
// API URL for your own IP (no IP needed, it auto-detects)
$url = "https://api.ipwho.org/me";

// Get the response JSON
$response = file_get_contents($url);

if ($response === FALSE) {
    die('Error fetching IP location data');
}

$data = json_decode($response, true);
print_r($data);
// Display some info
// echo "IP: " . $data['ip'] . "\n";
// echo "Country: " . $data['country'] . "\n";
// echo "City: " . $data['city'] . "\n";
// echo "Timezone: " . $data['timezone'] . "\n";
// echo "Currency: " . $data['currency']['code'] . "\n";

?>
