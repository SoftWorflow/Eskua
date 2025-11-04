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

$stmt = $conn->prepare("select f.id as fileId, f.original_name as fileOriginalName, size as fileSize from `files` as f join `public_materials_files` as pmf on f.id = pmf.file where pmf.public_material = ?;");
$stmt->execute([$materialId]);
$fileData = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

$stmt = $conn->prepare("select f.storage_name as storageName, pm.uploaded_date as uploadedDate from public_materials as pm join public_materials_files as pmf on pm.id = pmf.public_material join files as f on pmf.file = f.id where pm.id = ?;");
$stmt->execute([$materialId]);
$materialData = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();
$storageName = $materialData['storageName'];

$uploadedDate = new DateTime($materialData['uploadedDate']);

$year = $uploadedDate->format('Y');
$month = $uploadedDate->format('m');
$day = $uploadedDate->format('d');

$filePath = 'uploads/'.$year.'/'.$month.'/'.$storageName;

$materialData['filePath'] = $filePath;

$response = array_merge($materialData, $fileData);

echo json_encode($response);

?>