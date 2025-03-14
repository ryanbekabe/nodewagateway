<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Pesan WhatsApp</title>
</head>
<body>
    <h2>Kirim Pesan WhatsApp</h2><h3>Pesan Teks</h3>
    <form method="post">
        <label for="token">Token:</label><br>
        <input type="text" id="token" name="token" required><br><br>

        <label for="number">Nomor HP Tujuan (Format Internasional, tanpa +):</label><br>
        <input type="text" id="number" name="number" required><br><br>

        <label for="message">Pesan:</label><br>
        <textarea id="message" name="message" rows="4" required></textarea><br><br>

        <button type="submit" name="send_text">Kirim Pesan Teks</button>
    </form>

    <!-- 🔹 Form Kirim Pesan dengan Lampiran -->
    <h3>Pesan dengan Lampiran</h3>
    <form method="post">
        <label for="token">Token:</label><br>
        <input type="text" id="token" name="token" required><br><br>

        <label for="number">Nomor HP Tujuan (Format Internasional, tanpa +):</label><br>
        <input type="text" id="number" name="number" required><br><br>

        <label for="mediaUrl">URL Lampiran (contoh: https://example.com/image.jpg):</label><br>
        <input type="text" id="mediaUrl" name="mediaUrl" required><br><br>

        <label for="message">Pesan (opsional sebagai caption):</label><br>
        <textarea id="message" name="message" rows="4"></textarea><br><br>

        <button type="submit" name="send_media">Kirim Pesan dengan Lampiran</button>
    </form>

    <?php
    if (isset($_POST['send_text'])) {
        $token = $_POST['token'];
        $number = $_POST['number'];
        $message = $_POST['message'];

        $data = [
            'token' => $token,
            'number' => $number,
            'message' => $message
        ];

        $url = "http://localhost:3000/send-message";
        sendRequest($url, $data);
    }

    if (isset($_POST['send_media'])) {
        $token = $_POST['token'];
        $number = $_POST['number'];
        $mediaUrl = $_POST['mediaUrl'];
        $message = $_POST['message'] ?? "";

        $data = [
            'token' => $token,
            'number' => $number,
            'mediaUrl' => $mediaUrl,
            'message' => $message
        ];

        $url = "http://localhost:3000/send-message-lampiran";
        sendRequest($url, $data);
    }

    function sendRequest($url, $data) {
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
