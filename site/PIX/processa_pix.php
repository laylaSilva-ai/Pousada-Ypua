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

if (curl_errno($ch)) {
    echo 'Erro cURL: ' . curl_error($ch);
    curl_close($ch);
    exit;
}

curl_close($ch);

$response_data = json_decode($response, true);

// Verifica se houve erro na resposta da API
if (!isset($response_data['id'])) {
    echo "Erro ao gerar pagamento: resposta inválida da API Mercado Pago.";
    exit;
}

// Salva o ID real da transação
$mp_payment_id = $response_data['id'];

$stmt = $conn->prepare("UPDATE pagamentos SET mp_payment_id=? WHERE id=?");
$stmt->bind_param("si", $mp_payment_id, $payment_id);
$stmt->execute();
// Exibe QR Code e Código Pix (Copia e Cola)
if (isset($response_data['point_of_interaction']['transaction_data']['qr_code_base64'])) {
    $qr_code_base64 = $response_data['point_of_interaction']['transaction_data']['qr_code_base64'];
    $codigo_pix = $response_data['point_of_interaction']['transaction_data']['qr_code'];

    echo '<h3>Escaneie o QR Code com seu app bancário:</h3>';
echo '<img id="qrCodeImage" src="data:image/png;base64,' . $qr_code_base64 . '" alt="QR Code Pix" width="300" height="300">';
echo '<p id="countdown">Tempo restante: 2:00</p>';
echo '<div id="statusPagamento" style="margin-top:15px; font-weight:bold; font-size: 20px;"></div>';
echo '<button id="payButton" disabled style="margin-top: 10px;">Pagamento não disponível após expiração</button>';

// Copia e Cola com ID
echo '<div id="areaPix" style="margin-top:30px;">';
echo '<h4>Não conseguiu escanear o QR Code?</h4>';
echo '<p>Copie o código Pix abaixo e cole no seu app bancário:</p>';
echo '<textarea id="codigoPix" rows="4" style="width:100%; border-radius:8px; padding:10px;" readonly>' . $codigo_pix . '</textarea>';
echo '<button onclick="copiarPix()" style="margin-top:10px;">📋 Copiar código Pix</button>';
echo '</div>';

echo "<script>
    var minutes = 2;
    var seconds = 0;
    var countdownElement = document.getElementById('countdown');
    var qrCodeElement = document.getElementById('qrCodeImage');
    var payButton = document.getElementById('payButton');
    var statusDiv = document.getElementById('statusPagamento');
    var areaPix = document.getElementById('areaPix');

    var interval = setInterval(function() {
        if (seconds === 0) {
            if (minutes === 0) {
                clearInterval(interval);
                countdownElement.innerHTML = 'QR Code expirado!';
                payButton.disabled = true;
                qrCodeElement.style.display = 'none';
                statusDiv.innerHTML = '⏰ O QR Code expirou! Por favor, gere um novo.';
                areaPix.style.display = 'none';
                return;
            } else {
                minutes--;
                seconds = 59;
            }
        } else {
            seconds--;
        }
        countdownElement.innerHTML = 'Tempo restante: ' + minutes + ':' + (seconds < 10 ? '0' + seconds : seconds);
    }, 1000);

    // Verificação automática de status
    const id = '$mp_payment_id';
    const statusCheck = setInterval(() => {
        fetch('verificar_status.php?id=' + id)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'approved') {
                    clearInterval(statusCheck);
                    clearInterval(interval);
                    countdownElement.innerHTML = '';
                    statusDiv.innerHTML = '✅ Pagamento aprovado!';
                    statusDiv.style.fontSize = '32px';
                    statusDiv.style.color = 'green';
                    qrCodeElement.style.display = 'none';
                    areaPix.style.display = 'none';
                    payButton.disabled = true;
                } else if (data.status === 'rejected') {
                    clearInterval(statusCheck);
                    clearInterval(interval);
                    statusDiv.innerHTML = '❌ Pagamento rejeitado.';
                    qrCodeElement.style.display = 'none';
                    areaPix.style.display = 'none';
                    payButton.disabled = true;
                } else if (data.status === 'cancelled' || data.status === 'expired') {
                    clearInterval(statusCheck);
                    clearInterval(interval);
                    statusDiv.innerHTML = '⚠️ Pagamento expirado ou cancelado.';
                    qrCodeElement.style.display = 'none';
                    areaPix.style.display = 'none';
                    payButton.disabled = true;
                }
            });
    }, 5000);

    function copiarPix() {
        var texto = document.getElementById('codigoPix');
        texto.select();
        texto.setSelectionRange(0, 99999);
        document.execCommand('copy');
        alert('Código Pix copiado com sucesso!');
    }
</script>";

} else {
    echo "Erro ao gerar QR Code: " . $response_data['message'];
}    
$conn->close();
?>
