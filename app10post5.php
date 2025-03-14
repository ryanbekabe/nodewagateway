<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Pesan WhatsApp</title>
</head>
<body>
    <h2>Kirim Pesan WhatsApp</h2>
    <form method="post">
        <label for="token">Token:</label><br>
        <input type="text" id="token" name="token" required><br><br>

        <label for="number">Nomor HP Tujuan (Format Internasional, tanpa +):</label><br>
        <input type="text" id="number" name="number" required><br><br>

        <label for="message">Pesan:</label><br>
        <textarea id="message" name="message" rows="4" required></textarea><br><br>

        <button type="submit" name="send">Kirim Pesan</button>
    </form>

    <?php
    if (isset($_POST['send'])) {
        $token = $_POST['token'];
        $number = $_POST['number'];
        $message = $_POST['message'];

        $data = [
            'token' => $token,
            'number' => $number,
            'message' => $message
        ];

        $url = "http://localhost:3000/send-message"; // Sesuaikan dengan alamat server Node.js Anda
        $options = [
            "http" => [
                "header" => "Content-Type: application/json\r\n",
                "method" => "POST",
                "content" => json_encode($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            echo "<p style='color:red;'>Gagal mengirim pesan.</p>";
        } else {
            echo "<p style='color:green;'>Pesan berhasil dikirim!</p>";
        }
    }
    ?>
</body>
</html>
