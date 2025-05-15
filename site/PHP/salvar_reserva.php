<?php
// Conexão com o banco de dados 
$host = 'localhost';
$db   = 'pousadaaaa';
$user = 'root'; // ou outro usuário 
$pass = '';     // coloque a senha correta se necessário
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Verifica se o formulário foi enviado via POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Dados do formulário
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $ddd = $_POST['ddd'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $pedidos_especiais = $_POST['pedidos_especiais'] ?? '';
        $checkin = $_POST['checkin'] ?? '';
        $checkout = $_POST['checkout'] ?? '';
        $noites = $_POST['noites'] ?? 0;
        $numero_hospedes = $_POST['numero_hospedes'] ?? 0;
        $criancas = $_POST['criancas'] ?? 0;
        $total = $_POST['total'] ?? 0;
        
        // Debug - remova após testes
        // echo "<pre>";
        // print_r($_POST);
        // echo "</pre>";
        
        // Inserção no banco
        $stmt = $pdo->prepare("INSERT INTO reservas
            (nome, email, ddd, telefone, pedidos_especiais, checkin, checkout, noites, numero_hospedes, criancas, total)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
        $stmt->execute([
            $nome, 
            $email, 
            $ddd, 
            $telefone, 
            $pedidos_especiais, 
            $checkin, 
            $checkout, 
            $noites, 
            $numero_hospedes, 
            $criancas, 
            $total
        ]);
        
        echo "<script>alert('Reserva realizada com sucesso!'); window.location.href = '/site/HTML/home.html';</script>";
    } else {
        echo "<script>alert('Método de envio incorreto!'); window.location.href = '/site/HTML/home.html';</script>";
    }
} catch (PDOException $e) {
    echo "<script>alert('Erro ao salvar reserva: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
}
?>