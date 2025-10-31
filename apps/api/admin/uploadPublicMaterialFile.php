<?php

require_once(__DIR__ . "/../middleware/auth.php");
require_once(__DIR__ . "/../../../backend/db_connect.php");

require_once(__DIR__ . "/../../../backend/DTO/PublicMaterial.php");
require_once(__DIR__ . "/../../../backend/DTO/File.php");

require_once(__DIR__ . "/../../../backend/logic/file/FileLogicFacade.php");
require_once(__DIR__ . "/../../../backend/logic/material/MaterialLogicFacade.php");
header('Content-Type: application/json');

$auth = new AuthMiddleware();
$auth->authorize(['admin']);

$user = $auth::authenticate();
$userId = $user['user_id'];

$materialTitle = $_POST['title'] ?? '';
$materialDescription = $_POST['description'] ?? '';

if (empty($materialTitle)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'materialTitle' => 'El titulo del material es obligatorio']);
    exit;
}

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'No se recibió ningún archivo']);
    exit;
}

$db = new db_connect();
$pdo = $db->connect();

$file = $_FILES['file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error'=>'Error al subir archivo '.$file['error']]);
    exit;
}

$size = $file['size'];
if ($size > File::MAX_SIZE) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error'=>'El archivo es muy pesado']);
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
if (!in_array($mime, File::ALLOWED_MIME)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error'=>'Tipo de archivo inválido']);
    exit;
}

$origName = $file['name'];
$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
if (!in_array($ext, File::ALLOWED_EXTENSIONS)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Extension inválida']);
    exit;
}

$year = date('Y');
$month = date('m');
$uploadDirBase = FILE::FILE_PATH;
$targetDir = sprintf("%s/%s/%s", $uploadDirBase, $year, $month);

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0750, true);
}


// Random name + extension
$storageName = bin2hex(random_bytes(16)) . '.' . $ext;
$targetPath = $targetDir . '/' . $storageName;

$fileObject = new File($origName, $storageName, $mime, $ext, $size);
$material = new PublicMaterial($materialTitle, $materialDescription);

$materialLogic = MaterialLogicFacade::getInstance()->getIMaterialLogic();

if (!$materialLogic->uploadMaterial($material, $fileObject, $userId)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Hubo un error al crear el material']);
    exit;
}

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Fallo al mover el archivo']);
    exit;
}

echo json_encode(['ok' => true, 'message' => 'El archivo "'.$file["name"].'" se subió exitosamente']);

?>