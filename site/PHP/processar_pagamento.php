<?php
require 'vendor/autoload.php';

// Coloque sua Access Token aqui (essa é secreta e deve ficar só no backend)
MercadoPago\SDK::setAccessToken('SUA_ACCESS_TOKEN_AQUI');

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Dados inválidos."]);
    exit;
}

$payment = new MercadoPago\Payment();
$payment->transaction_amount = $data['transaction_amount'];
$payment->token = $data['token'];
$payment->description = "Reserva na Pousada Quinta do Ypuã";
$payment->installments = $data['installments'];
$payment->payment_method_id = $data['payment_method_id'];
$payment->payer = array(
    "email" => $data['payer']['email'],
    "identification" => array(
        "type" => $data['payer']['identification']['type'],
        "number" => $data['payer']['identification']['number']
    )
);

if ($payment->save()) {
    if ($payment->status == 'approved') {
        echo json_encode(["status" => "approved", "message" => "Pagamento aprovado!"]);
    } else {
        echo json_encode([
            "status" => $payment->status,
            "status_detail" => $payment->status_detail,
            "message" => "Pagamento não aprovado."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Erro ao processar o pagamento.",
        "error" => $payment->error
    ]);
}
?>
