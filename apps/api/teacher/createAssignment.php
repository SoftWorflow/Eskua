<?php

require_once(__DIR__ . "/../middleware/auth.php");

require_once(__DIR__ . "/../../../backend/DTO/GroupAssignment.php");
require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/DTO/File.php");

require_once(__DIR__ . "/../../../backend/logic/assignment/AssignmentLogicFacade.php");
require_once(__DIR__ . "/../../../backend/logic/file/FileLogicFacade.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
header('Content-Type: application/json');

$auth = new AuthMiddleware();
$auth::authorize(['teacher']);

$user = $auth::authenticate();
$teacherId = $user['user_id'];

$name = $_POST['title'] ?? '';
$descrption = $_POST['description'] ?? '';
$maxScore = $_POST['maxScore'] ?? '';
$dueDateRaw = $_POST['dueDate'] ?? '';
$groupId = $_POST['groupId'] ?? '';

$errorResponse['ok'] = true;
if (empty($name)) {
    $errorResponse['ok'] = false;
    $errorResponse['error'][] = 'La tarea debe de tener un nombre';
}

if (empty($descrption)) {
    $errorResponse['ok'] = false;
    $errorResponse['error'][] = 'La tarea debe de tener una descripción';
}

if (empty($maxScore)) {
    $errorResponse['ok'] = false;
    $errorResponse['error'][] = 'La tarea debe de tener un puntaje';
}

if (empty($dueDateRaw)) {
    $errorResponse['ok'] = false;
    $errorResponse['error'][] = 'La tarea debe de tener una fecha de vencimiento';
}

if (empty($groupId)) {
    $errorResponse['ok'] = false;
    $errorResponse['error'][] = 'No se recibió el grupo';
}

if (!$errorResponse['ok']) {
    http_response_code(400);
    echo json_encode($errorResponse);
    exit;
}

$currentTime = new DateTime('now');

$dueDate = DateTime::createFromFormat('d-m-Y', $dueDateRaw);

$dueDate->setTime(
    (int)$currentTime->format('H'),
    (int)$currentTime->format('i'),
    (int)$currentTime->format('s')
);

if ($dueDate < $currentTime) {
    http_response_code(400);
    echo json_encode(['ok' => false, ['dueDate' => 'La tarea debe de vencer antes de que comienze']]);
    exit;
}

if (strlen($name) > 50) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'El titulo debe de ser menor a 50 caracteres']);
    exit;
}

if ($maxScore <= 0 ) {
    http_response_code(400);
    echo json_encode(['ok'=> false, 'error'=> $maxScore < 0 ? 'El puntaje no puede ser negativo' : 'El puntaje tiene que ser mayor a 0']);
    exit;
}

$assignment = new GroupAssignment($name, $descrption, $maxScore, $groupId, $dueDate);

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();

if (isset($_FILES['file'])) {
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

    $assignmentLogic = AssignmentLogicFacade::getInstance()->getIAssignmentLogic();

    if (!$assignmentLogic->createAssignment($assignment, $fileObject, $teacherId)) {
        http_response_code(500);
        echo json_encode(['ok' => false,'message' => 'No se puedo crear la tarea']);
    } else {
        http_response_code(200);
        echo json_encode(['ok' => true, 'message' => 'La tarea fué creada con éxito']);
    }

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Fallo al mover el archivo']);
        exit;
    }
} else {
    if (!$userLogic->createAssignment($assignment, $teacherId)) {
        http_response_code(500);
        echo json_encode(['ok' => false,'message' => 'No se puedo crear la tarea']);
    } else {
        http_response_code(200);
        echo json_encode(['ok' => true, 'message' => 'La tarea fué creada con éxito']);
    }
}

?>