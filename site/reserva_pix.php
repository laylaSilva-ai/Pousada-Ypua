<?php
// Seu Access Token do Mercado Pago
$access_token = 'TEST-8396377814695066-050520-a24b71ffa3f5be743f9c9b0246ba7079-2426274282';

// Dados do cliente e valor da reserva
$valor = 400.00; // Valor da reserva em R$
$descricao = "Reserva - Pousada Beira-Mar";
$email_cliente = "cliente@email.com";

// Configura a requisição
$url = "https://api.mercadopago.com/v1/payments";
$dados = [
    "transaction_amount" => $valor,
    "description" => $descricao,
    "payment_method_id" => "pix",
    "payer" => [
        "email" => $email_cliente
    ]
];

$headers = [
    "Authorization: Bearer $access_token",
    "Content-Type: application/json"
];

// Envia a requisição para gerar o pagamento Pix
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$resposta = curl_exec($ch);
curl_close($ch);

// Decodifica a resposta
$resposta = json_decode($resposta, true);

// Verifica se veio o QR Code
if (isset($resposta['point_of_interaction']['transaction_data'])) {
    $qr_base64 = $resposta['point_of_interaction']['transaction_data']['qr_code_base64'];
    $codigo_copia_colar = $resposta['point_of_interaction']['transaction_data']['qr_code'];
} else {
    echo "Erro ao gerar pagamento Pix.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pagamento Pix - Pousada</title>
</head>
<body>
    <h2>Pagamento via Pix</h2>
    <p>Valor: <strong>R$ <?= number_format($valor, 2, ',', '.') ?></strong></p>
    <p><strong>Escaneie o QR Code abaixo:</strong></p>
    <img src="data:image/png;base64,<?= $qr_base64 ?>" alt="QR Code Pix">

    <p>Ou copie o código Pix:</p>
    <textarea cols="60" rows="3"><?= $codigo_copia_colar ?></textarea>

    <p>Aguardando confirmação de pagamento...</p>
</body>
</html>
