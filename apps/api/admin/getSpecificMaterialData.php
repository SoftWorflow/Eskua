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

$stmt = $conn->prepare("select pm.id, pm.title, pm.description, pm.uploaded_date as uploadedDate, f.extension as type from public_materials as pm join public_materials_files as pmf on pm.id = pmf.public_material join files as f on pmf.file = f.id where pm.id = ?;");
$stmt->execute([$materialId]);
$materialData = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

echo json_encode($materialData);

?>