<?php
$access_token = "APP_USR-8396377814695066-050520-e2f5c2a31b6777f1ed1ce48adfdbcd0a-2426274282"; // Seu token Mercado Pago
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pousada_ypua";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

$email = $_POST['email'] ?? null;
$valor = $_POST['valor'] ?? null;

if (!$email || !$valor) {
    echo "E-mail ou valor não foram enviados corretamente.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "E-mail inválido.";
    exit;
}

if (!is_numeric($valor)) {
    echo "O valor não é numérico.";
    exit;
}

$valor = str_replace(',', '.', $valor);
$valor = (float) $valor;

$stmt = $conn->prepare("INSERT INTO pagamentos (email, valor, status) VALUES (?, ?, 'pendente')");
$stmt->bind_param("sd", $email, $valor);
$stmt->execute();
$payment_id = $stmt->insert_id;

$idempotency_key = uniqid('pay_', true);

$payment_data = [
    "transaction_amount" => $valor,
    "description" => "Pagamento via Pix",
    "payment_method_id" => "pix",
    "payer" => [
        "email" => $email
    ]
];

$url = 'https://api.mercadopago.com/v1/payments';
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payment_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token,
    'X-Idempotency-Key: ' . $idempotency_key
]);

$response = curl_exec($ch);
if(curl_errno($ch)) {
    echo 'Erro cURL: ' . curl_error($ch);
    exit;
}
curl_close($ch);

$response_data = json_decode($response, true);

if (isset($response_data['point_of_interaction']['transaction_data']['qr_code_base64'])) {
    $qr_code_base64 = $response_data['point_of_interaction']['transaction_data']['qr_code_base64'];
    echo '<h3>Escaneie o QR Code com seu app bancário:</h3>';
    echo '<img id="qrCodeImage" src="data:image/png;base64,' . $qr_code_base64 . '" alt="QR Code Pix" width="300" height="300">';
    echo '<p id="countdown">Tempo restante: 2:00</p>';
    echo '<button id="payButton" disabled>Pagamento não disponível após expiração</button>';

    echo '<script>
        var minutes = 2;
        var seconds = 0;
        var countdownElement = document.getElementById("countdown");
        var qrCodeElement = document.getElementById("qrCodeImage");
        var payButton = document.getElementById("payButton");
        
        var interval = setInterval(function() {
            if (seconds === 0) {
                if (minutes === 0) {
                    clearInterval(interval);
                    countdownElement.innerHTML = "QR Code expirado!";
                    payButton.disabled = true;
                    qrCodeElement.style.display = "none";
                    alert("O QR Code expirou! Por favor, gere um novo.");
                } else {
                    minutes--;
                    seconds = 59;
                }
            } else {
                seconds--;
            }
            countdownElement.innerHTML = "Tempo restante: " + minutes + ":" + (seconds < 10 ? "0" + seconds : seconds);
        }, 1000);
    </script>';

} else {
    echo "Erro: QR Code não disponível.";
}

// Verificando o status do pagamento
if (isset($response_data['status'])) {
    $status = $response_data['status']; // Aqui você obtém o status do pagamento

    // Atualizar o banco de dados com o status
    $stmt = $conn->prepare("UPDATE pagamentos SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $payment_id);
    $stmt->execute();

    // Exibir a mensagem no frontend
    if ($status == 'approved') {
        echo "<p>Pagamento realizado com sucesso!</p>";
    } elseif ($status == 'rejected') {
        echo "<p>Pagamento rejeitado! Tente novamente.</p>";
    } else {
        echo "<p>Aguardando pagamento... Aguarde confirmação.</p>";
    }
} else {
    echo "Erro ao comunicar com a API MercadoPago.";
}

$conn->close();
?>
