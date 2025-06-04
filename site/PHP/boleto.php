<?php
// boleto.php

// Habilita a exibição de erros para debug (remova em produção)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// INSIRA SEU ACCESS_TOKEN DO MERCADO PAGO AQUI
$access_token = 'APP_USR-8396377814695066-050520-e2f5c2a31b6777f1ed1ce48adfdbcd0a-2426274282'; // Substitua pelo token correto

header('Content-Type: application/json');

$data_raw = file_get_contents("php://input");
if (!$data_raw) {
    http_response_code(400);
    echo json_encode(["error" => "Nenhum dado recebido."]);
    exit;
}

$dados = json_decode($data_raw, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["error" => "JSON inválido: " . json_last_error_msg()]);
    exit;
}

if (!isset($dados['payment_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "payment_id ausente."]);
    exit;
}

$payment_id = intval($dados['payment_id']);
$url = "https://api.mercadopago.com/v1/payments/{$payment_id}?access_token=" . $access_token;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
if ($response === false) {
    $error_msg = curl_error($ch);
    error_log("cURL Error: " . $error_msg);
    curl_close($ch);
    http_response_code(500);
    echo json_encode(["error" => "Erro no cURL: " . $error_msg]);
    exit;
}
curl_close($ch);

// Registra a resposta completa da API no log para debug
error_log("Resposta da API (Consulta): " . $response);

// DESCOMENTE as linhas abaixo temporariamente para exibir a resposta de debug
// if (isset($_GET['debug']) && $_GET['debug'] == 1) {
//     echo $response;
//     exit;
// }

$resultado = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao decodificar JSON: " . json_last_error_msg()]);
    exit;
}

if (isset($resultado['error'])) {
    http_response_code(500);
    $error_message = isset($resultado['message']) ? $resultado['message'] : 'Erro ao consultar pagamento.';
    echo json_encode(["error" => $error_message]);
    exit;
}

echo json_encode([
    "status"             => isset($resultado['status']) ? $resultado['status'] : "Desconhecido",
    "payment_method_id"  => isset($resultado['payment_method_id']) ? $resultado['payment_method_id'] : "",
    "payer"              => isset($resultado['payer']) ? $resultado['payer'] : null
]);

exit;
?>
