<?php
// Exibe erros na tela (para desenvolvimento)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuração do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sousada";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica conexão
if ($conn->connect_error) {
    die("<h2>Erro ao conectar no banco de dados:</h2><p>" . $conn->connect_error . "</p>");
}
?>
