<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados do hóspede
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $ddd = $_POST['ddd'];
    $telefone = $_POST['telefone'];
    $pedidos = $_POST['pedidos_especiais'];

    // Captura os dados da reserva
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $noites = $_POST['noites'];
    $hospedes = $_POST['numero_hospedes'];
    $criancas = isset($_POST['criancas']) ? $_POST['criancas'] : 0;
    $total = $_POST['total'];

    // 1. Inserir hóspede
    $stmtHospede = $conn->prepare("INSERT INTO hospedes (nome, email, ddd, telefone, pedidos_especiais) VALUES (?, ?, ?, ?, ?)");
    $stmtHospede->bind_param("sssss", $nome, $email, $ddd, $telefone, $pedidos);

    if ($stmtHospede->execute()) {
        $hospede_id = $stmtHospede->insert_id;

        // 2. Inserir reserva (inclui número de crianças)
        $stmtReserva = $conn->prepare("
            INSERT INTO reservas (
                hospede_id, checkin, checkout, noites, numero_hospedes, criancas, total
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmtReserva->bind_param("issiiid", $hospede_id, $checkin, $checkout, $noites, $hospedes, $criancas, $total);

        if ($stmtReserva->execute()) {
            echo "Reserva salva com sucesso!";
        } else {
            echo "Erro ao salvar a reserva: " . $stmtReserva->error;
        }

        $stmtReserva->close();
    } else {
        echo "Erro ao salvar o hóspede: " . $stmtHospede->error;
    }

    $stmtHospede->close();
    $conn->close();
} else {
    echo "Requisição inválida.";
}
?>
