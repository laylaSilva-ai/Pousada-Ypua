<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pousada_ypua";


try {
    // Corrigido: uso das variáveis certas
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se os dados foram enviados via POST
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $ddd = $_POST['ddd'];
        $telefone = $_POST['telefone'];
        $pedidos = $_POST['pedidos_especiais'];
        $checkin = $_POST['checkin'];
        $checkout = $_POST['checkout'];
        $noites = $_POST['noites'];
        $hospedes = $_POST['numero_hospedes'];
        $criancas = $_POST['criancas'];
        $total = $_POST['total'];

        // Prepara e executa o insert
        $stmt = $conn->prepare("INSERT INTO reservas 
            (nome, email, ddd, telefone, pedidos_especiais, checkin, checkout, noites, numero_hospedes, criancas, total) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $nome, $email, $ddd, $telefone, $pedidos, $checkin, $checkout, $noites, $hospedes, $criancas, $total
        ]);


        // Mensagem de sucesso
        echo "<script>alert('Reserva realizada com sucesso!'); window.location.href='../HTML/escolha_pagamento.html';</script>";

        exit;
    }

} catch (PDOException $e) {
    echo "Erro ao conectar ou inserir: " . $e->getMessage();
    exit;
}
?>

