<?php
function sendMessage($token, $number, $message) {
    $url = "http://localhost:3000/send-message";
    
    $data = [
        "token" => $token,
        "number" => $number,
        "message" => $message
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

$token = "a1216fb9e938b1ed78763abc82284e7f56f4c72ddb7dce756b7b216609183c1f"; // Ganti dengan token pengguna yang valid
$number = "6282254205110"; // Ganti dengan nomor tujuan
$message = "Tes token a1216f";

$response = sendMessage($token, $number, $message);
echo $response;
?>
