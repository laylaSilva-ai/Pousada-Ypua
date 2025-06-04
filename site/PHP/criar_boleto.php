<?php
// criar_boleto.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Substitua pelo seu token real do Mercado Pago
$access_token = "APP_USR-8396377814695066-050520-e2f5c2a31b6777f1ed1ce48adfdbcd0a-2426274282";


header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Ajuste para o domínio do front-end em produção
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Recebe os dados enviados pelo front-end
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

// Loga os dados recebidos
error_log("Dados recebidos: " . print_r($dados, true));

// Verifica se os dados obrigatórios estão presentes
if (!isset($dados['total']) || !isset($dados['nome']) || !isset($dados['email'])) {
    http_response_code(400);
    echo json_encode(["error" => "Dados obrigatórios ausentes (total, nome, email)."]);
    exit;
}

// Prepara os dados de pagamento para a API do Mercado Pago
$payment_data = [
    "transaction_amount" => (float)$dados['total'],
    "description" => "Pagamento da reserva - " . $dados['nome'],
    "payment_method_id" => "boleto",
    "payer" => [
        "email" => $dados['email']
    ]
];

$url = "https://api.mercadopago.com/v1/payments";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payment_data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

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

// Loga a resposta da API
error_log("Resposta da API Mercado Pago: " . $response);

// Debug: exibe a resposta bruta se ?debug=1 for passado
if (isset($_GET['debug']) && $_GET['debug'] == 1) {
    echo $response;
    exit;
}

$resultado = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao decodificar a resposta JSON: " . json_last_error_msg()]);
    exit;
}

// Verifica erros na resposta da API
if (isset($resultado['error']) || (isset($resultado['status']) && $resultado['status'] == 'rejected')) {
    http_response_code(500);
    $error_message = isset($resultado['message']) ? $resultado['message'] : 'Erro ao gerar pagamento.';
    if (isset($resultado['cause']) && is_array($resultado['cause'])) {
        foreach ($resultado['cause'] as $cause) {
            $error_message .= ' | ' . $cause['code'] . ': ' . $cause['description'];
        }
    }
    error_log("Erro da API Mercado Pago: " . $error_message);
    echo json_encode(["error" => $error_message]);
    exit;
}

if (!isset($resultado['id'])) {
    http_response_code(500);
    echo json_encode(["error" => "Falha ao gerar boleto: ID do pagamento não recebido."]);
    exit;
}

// Extrai dados úteis da resposta
$link_boleto = isset($resultado['transaction_details']['external_resource_url']) ? $resultado['transaction_details']['external_resource_url'] : '';
$codigo_barras = isset($resultado['barcode']['content']) ? $resultado['barcode']['content'] : 'Não disponível';

echo json_encode([
    "id_pagamento" => $resultado['id'],
    "url_boleto" => $link_boleto,
    "codigo_barras" => $codigo_barras
]);

exit;
?>