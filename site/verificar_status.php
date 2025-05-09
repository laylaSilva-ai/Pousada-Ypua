<?php
$access_token = "APP_USR-8396377814695066-050520-e2f5c2a31b6777f1ed1ce48adfdbcd0a-2426274282";
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pousada_ypua";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro ao conectar no banco de dados: " . $conn->connect_error);
}

// Pega pagamentos ainda pendentes ou em processamento
$result = $conn->query("SELECT id, mp_payment_id FROM pagamentos WHERE status IN ('pendente', 'em_processamento') AND mp_payment_id IS NOT NULL");

while ($row = $result->fetch_assoc()) {
    $local_id = $row['id'];
    $mp_payment_id = $row['mp_payment_id'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/v1/payments/$mp_payment_id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $access_token
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    $status_api = $data['status'] ?? 'desconhecido';

    switch ($status_api) {
        case 'approved':
            $status = 'aprovado';
            break;
        case 'pending':
            $status = 'pendente';
            break;
        case 'in_process':
            $status = 'em_processamento';
            break;
        case 'rejected':
            $status = 'rejeitado';
            break;
        default:
            $status = 'desconhecido';
    }

    $stmt = $conn->prepare("UPDATE pagamentos SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $local_id);
    $stmt->execute();

    echo "Pagamento ID $local_id atualizado para '$status'.<br>";
}

$conn->close();
?>
