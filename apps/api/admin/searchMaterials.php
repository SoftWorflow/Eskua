<?php

require_once(__DIR__ . "/../middleware/auth.php");
require_once(__DIR__ . "/../../../backend/db_connect.php");
header('Content-Type: application/json');

$auth = new AuthMiddleware();
$auth->authorize(['admin']);

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!$data && !isset($data['title'])) exit;

$materialTitle = $data['title'];

$dbConnection = new db_connect();
$conn = $dbConnection->connect();

$stmt = $conn->prepare("select pm.id as id, pm.title as title, f.extension as `type` from public_materials as pm join public_materials_files as pmf on pm.id = pmf.public_material join files as f on pmf.file = f.id where pm.title like ?;");
$stmt->execute(["%" . $materialTitle . "%"]);
$materialsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt->closeCursor();

if (empty($materialsData)) {
    echo json_encode(['ok' => false, 'message' => 'No se encontraron resultados']);
} else {
    echo json_encode(['ok' => true, $materialsData]);
}

?>