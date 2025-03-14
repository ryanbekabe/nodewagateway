<?php
$apiUrl = "http://localhost:3000/register";
$data = [
    "number" => "6282183981313",
];
// Response: {"status":true,"message":"User registered successfully","token":"61360753ce0e7554c36ff5556fd810ae"}

$options = [
    "http" => [
        "header"  => "Content-Type: application/json\r\n",
        "method"  => "POST",
        "content" => json_encode($data),
    ],
];

$context  = stream_context_create($options);
$result = file_get_contents($apiUrl, false, $context);

if ($result === FALSE) {
    echo "Failed to send message.";
} else {
    echo "Response: " . $result;
}
?>
