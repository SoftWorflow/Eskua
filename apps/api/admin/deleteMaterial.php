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

$stmt = $conn->prepare("select pm.uploaded_date as uploadedDate, f.storage_name as storageName from public_materials as pm join public_materials_files as pmf on pm.id = pmf.public_material join files as f on pmf.file = f.id where pm.id = ?;");
$stmt->execute([$materialId]);
$materialData = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

$uploadedDate = new DateTime($materialData['uploadedDate']);
$storageName = $materialData['storageName'];

$year = $uploadedDate->format('Y');
$month = $uploadedDate->format('m');
$day = $uploadedDate->format('d');

$filePath = __DIR__ . '/../../uploads/'.$year.'/'.$month.'/'.$storageName;

if (file_exists($filePath)) {
    if (!unlink($filePath)) {
        $response = ['ok' => false, 'message' => 'Hubo un error al eliminar el material'];
        echo json_encode($response);
        exit;
    }
} else {
    $response = ['ok' => false, 'message' => 'No se encontró el archivo'];
    echo json_encode($response);
    exit;
}

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