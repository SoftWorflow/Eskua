<?php

require_once(__DIR__ . "/../middleware/auth.php");
require_once(__DIR__ . "/../../../backend/db_connect.php");
header('Content-Type: application/json');

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!$data && !isset($data['id'])) exit;

$materialId = $data['id'];

$auth = new AuthMiddleware();
$auth->authorize(['admin']);

$dbConnection = new db_connect();
$conn = $dbConnection->connect();

$stmt = $conn->prepare("call fDeleteMaterial(?)");
$stmt->execute([$materialId]);
$affectedRows = $stmt->rowCount();

if ($affectedRows > 0) {
    $response = ['ok' => true, 'message' => 'Material borrado con exito!'];
} else {
    http_response_code(501);
    $response = ['ok' => false, 'message' => 'Hubo un error al eliminar el material!'];
}

echo json_encode($response);

?>