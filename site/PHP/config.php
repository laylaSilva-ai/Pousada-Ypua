<?php
$access_token = "APP_USR-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pousada_ypua";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro ao conectar no banco de dados: " . $conn->connect_error);
}
