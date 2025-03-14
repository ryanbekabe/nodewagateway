<?php
function generateToken($number) {
    return hash("sha256", $number);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $number = $_POST['number'];
    $data = ["number" => $number];
    
    $ch = curl_init("http://localhost:3000/register");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);

    $token = generateToken($number);
    echo "Token: " . $token;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register WhatsApp</title>
</head>
<body>
    <h2>Register WhatsApp Number</h2>
    <form method="POST">
        <label for="number">Phone Number:</label>
        <input type="text" name="number" id="number" required>
        <button type="submit">Register</button>
    </form>
    
    <?php if (isset($result)) : ?>
        <p><?php echo $result['message']; ?></p>
    <?php endif; ?>
</body>
</html>
