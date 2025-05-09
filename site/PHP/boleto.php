<?php
require 'vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Definindo cabeçalhos
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "viagens";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Erro na conexão com o banco: " . $conn->connect_error]));
}

// Recebendo os dados da requisição
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['pdf'])) {
    echo json_encode(["success" => false, "message" => "Dados incompletos."]);
    exit;
}

$email = $data['email'];
$pdfBase64 = $data['pdf'];

// Decodificando o PDF
$pdfContent = explode(",", $pdfBase64)[1];
$pdfBinary = base64_decode($pdfContent);

// Inserindo no banco de dados corretamente
$stmt = $conn->prepare("INSERT INTO boletos (email, pdf) VALUES (?, ?)");
$stmt->bind_param("s", $email);
$stmt->send_long_data(1, $pdfBinary);
$stmt->execute();
$stmt->close();

// Configurando o PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'juliamedeiroschaves043@gmail.com'; 
    $mail->Password = 'mnluolmzqopckape';  
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Configuração do remetente e destinatário
    $mail->setFrom('juliamedeiroschaves043@gmail.com', 'Viagens');
    $mail->addAddress($email);

    // Adicionando o anexo
    $mail->addStringAttachment($pdfBinary, 'boletos.pdf', 'base64', 'application/pdf');

    // Configurações do e-mail
    $mail->isHTML(true);
    $mail->Subject = 'Boletos de Pagamento para Viagem';
    $mail->Body = 'Segue em anexo os boletos de pagamento para sua viagem.';

    // Enviando o e-mail
    $mail->send();

    echo json_encode(["success" => true, "message" => "E-mail enviado com sucesso!"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Erro ao enviar o e-mail: {$mail->ErrorInfo}"]);
}

// Fechando a conexão
$conn->close();
?>

