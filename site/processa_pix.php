<?php
require _DIR_ . '/vendor/autoload.php'; // SDK do Mercado Pago

// Substitua pelo seu Access Token
MercadoPago\SDK::setAccessToken('SEU_ACCESS_TOKEN_AQUI');

// Receber os dados do formulário
$nome = $_POST['nome'];
$valor = floatval($_POST['valor']);
$descricao = $_POST['descricao'];
$email = $_POST['email'];

$payment = new MercadoPago\Payment();
$payment->transaction_amount = $valor;
$payment->description = $descricao;
$payment->payment_method_id = "pix";

$payer = new MercadoPago\Payer();
$payer->email = $email;
$payer->first_name = $nome;

$payment->payer = $payer;
$payment->save();

// Exibir QR Code se estiver pendente
if ($payment->status === "pending") {
    echo "<h3>Pague com Pix</h3>";
    echo "<img src='{$payment->point_of_interaction->transaction_data->qr_code_base64}' /><br><br>";
    echo "<label>Ou copie o código abaixo:</label><br>";
    echo "<textarea rows='6' cols='70'>{$payment->point_of_interaction->transaction_data->qr_code}</textarea><br><br>";
    echo "<strong>Status:</strong> {$payment->status}";
} else {
    echo "Erro ao gerar pagamento: " . $payment->status_detail;
}
?>