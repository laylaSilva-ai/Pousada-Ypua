<?php
// Dados fornecidos (dados de exemplo que você forneceu)
$data = [
    'email' => 'laylasilva@gmail.com',
    'valor' => 110,
    'accounts_info' => null,
    'acquirer_reconciliation' => [],
    'additional_info' => [
        'tracking_id' => 'platform:v1-whitelabel,so:ALL,type:N/A,security:none'
    ],
    'authorization_code' => null,
    'binary_mode' => null,
    'brand_id' => null,
    'build_version' => '3.104.0-rc-3',
    'call_for_authorize_id' => null,
    'callback_url' => null,
    'captured' => 1,
    'card' => [],
    'charges_details' => [
        [
            'accounts' => [
                'from' => 'collector',
                'to' => 'mp'
            ],
            'amounts' => [
                'original' => 1.09,
                'refunded' => 0
            ],
            'client_id' => 0,
            'date_created' => '2025-05-08T18:15:35.157-04:00',
            'id' => '110941527344-001',
            'last_updated' => '2025-05-08T18:15:35.157-04:00',
            'metadata' => [
                'reason' => '',
                'source' => 'rule-engine'
            ],
            'name' => 'mercadopago_fee',
            'refund_charges' => [],
            'reserve_id' => null,
            'type' => 'fee'
        ]
    ],
    'status' => 'pending',
    'status_detail' => 'pending_waiting_transfer',
    'transaction_amount' => 110,
    'transaction_details' => [
        'total_paid_amount' => 110
    ],
];

// Função para validar o e-mail
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Função para validar se o valor é um número positivo
function validarValor($valor) {
    return is_numeric($valor) && $valor > 0;
}

// Função para validar a consistência dos dados
function validarDados($data) {
    // Validação de e-mail
    if (!validarEmail($data['email'])) {
        echo "Erro: E-mail inválido.<br>";
        return false;
    }

    // Validação do valor do pagamento
    if (!validarValor($data['valor'])) {
        echo "Erro: Valor do pagamento inválido.<br>";
        return false;
    }

    // Validação do status
    if (empty($data['status'])) {
        echo "Erro: Status do pagamento não encontrado.<br>";
        return false;
    }

    // Validação de charges_details (deve existir e ser um array não vazio)
    if (empty($data['charges_details']) || !is_array($data['charges_details'])) {
        echo "Erro: Detalhes da cobrança não encontrados.<br>";
        return false;
    }

    // Validando o valor total pago na transação
    if ($data['transaction_details']['total_paid_amount'] != $data['valor']) {
        echo "Erro: O valor total pago não corresponde ao valor informado na transação.<br>";
        return false;
    }

    return true;
}

// Validar os dados antes de continuar
if (!validarDados($data)) {
    exit(); // Se a validação falhar, interrompe o processo
}

// Se os dados forem válidos, redirecionar para o processo de pagamento
header('Location: processa_pagamento.php');
exit();
?>
