<?php
$host = "localhost";
$dbname = "pousadaaaa";
$username = "root"; // Altere se necessário
$password = ""; // Altere se necessário

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>