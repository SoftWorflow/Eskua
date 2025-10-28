<?php

require_once(__DIR__ . "/../middleware/auth.php");
require_once(__DIR__ . "/../../../backend/db_connect.php");
header('Content-Type: application/json');

$auth = new AuthMiddleware();
$auth->authorize(['admin']);

$dbConnection = new db_connect();
$conn = $dbConnection->connect();

$stmt = $conn->prepare("select pm.id as id, pm.title as title, f.extension as `type` from public_materials as pm join public_materials_files as pmf on pm.id = pmf.public_material join files as f on pmf.file = f.id;");
$stmt->execute();
$materialsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt->closeCursor();

if (!$materialsData) {
    $materialsData['ok'] = false;
} else {
    $materialsData['ok'] = true;
}

echo json_encode($materialsData);

?>