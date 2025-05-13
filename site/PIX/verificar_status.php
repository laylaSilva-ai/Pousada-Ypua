<?php
session_start();
$access_token = "APP_USR-8396377814695066-050520-e2f5c2a31b6777f1ed1ce48adfdbcd0a-2426274282";
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pousada_ypua";
$transaction_id = $_SESSION['transaction_id'] ?? $_GET['id'] ?? null;


if (!$transaction_id) {
    echo json_encode(['status' => 'indefinido']);
    exit;
}

// Conecta ao banco
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['status' => 'erro', 'mensagem' => $conn->connect_error]));
}

// Verifica se já está aprovado no banco
$stmt = $conn->prepare("SELECT status FROM pagamentos WHERE mp_payment_id = ?");
$stmt->bind_param("s", $transaction_id);
$stmt->execute();
$result = $stmt->get_result();
$status_local = $result->fetch_assoc()['status'] ?? null;

if ($status_local === 'approved') {
    echo json_encode(['status' => 'approved']);
    exit;
}

// Se ainda não estiver aprovado, consulta a API do Mercado Pago
$ch = curl_init("https://api.mercadopago.com/v1/payments/$transaction_id");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $access_token"
]);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$status_api = $data['status'] ?? 'unknown';

// Atualiza o status no banco
$stmt = $conn->prepare("UPDATE pagamentos SET status = ? WHERE mp_payment_id = ?");
$stmt->bind_param("ss", $status_api, $transaction_id);
$stmt->execute();

// Retorna o status para o frontend
echo json_encode(['status' => $status_api]);
?>
