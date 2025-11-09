<?php

require_once(__DIR__ . "/../../middleware/auth.php");

require_once('IAssignmentLogic.php');
require_once(__DIR__ . "/../../DTO/GroupAssignment.php");
require_once(__DIR__ . "/../../DTO/File.php");
require_once(__DIR__ . "/../../persistence/assignment/AssignmentPersistenceFacade.php");

class AssignmentLogic implements IAssignmentLogic {

    public const FILE_PATH = '/var/www/html/uploads';
    public const MAX_SIZE = 20 * 1024 * 1024; // 20 MB
    public const ALLOWED_MIME = [
        'application/pdf',
        'image/png',
        'image/jpeg',
        'image/webp',
        'video/mp4'
    ];
    public const ALLOWED_EXTENSIONS = ['pdf','png','jpg','jpeg','webp', 'mp4'];
    
    public function createAssignment(GroupAssignment $assignment, ?array $file = null): array {
        AuthMiddleware::authorize(['teacher']);
        $teacherId = AuthMiddleware::authenticate()['user_id'];
        
        $assignmentPersistence = AssignmentPersistenceFacade::getInstance()->getIAssignmentPersistence();
        
        $errorResponse['ok'] = true;
        if (empty($assignment->getName())) {
            $errorResponse['ok'] = false;
            $errorResponse['error'][] = 'La tarea debe de tener un nombre';
        }

        if (empty($assignment->getDescription())) {
            $errorResponse['ok'] = false;
            $errorResponse['error'][] = 'La tarea debe de tener una descripción';
        }

        if (empty($assignment->getMaxScore())) {
            $errorResponse['ok'] = false;
            $errorResponse['error'][] = 'La tarea debe de tener un puntaje';
        }

        if (empty($assignment->getDueDate())) {
            $errorResponse['ok'] = false;
            $errorResponse['error'][] = 'La tarea debe de tener una fecha de vencimiento';
        }

        if (empty($assignment->getGroupId())) {
            $errorResponse['ok'] = false;
            $errorResponse['error'][] = 'No se recibió el grupo';
        }

        if (!$errorResponse['ok']) {
            http_response_code(400);
            return $errorResponse;
        }
        
        $currentTime = new DateTime('now');

        $dueDate = DateTime::createFromFormat('d-m-Y', $assignment->getDueDate()->format('d-m-Y'));
    
        $dueDate->setTime(
            (int)$currentTime->format('H'),
            (int)$currentTime->format('i'),
            (int)$currentTime->format('s')
        );

        if ($dueDate < $currentTime) {
            http_response_code(400);
            return ['ok' => false, 'dueDate' => 'La tarea debe de vencer antes de que comienze'];
        }

        if (strlen($assignment->getName()) > 50) {
            http_response_code(400);
            return ['ok' => false, 'error' => 'El titulo debe de ser menor a 50 caracteres'];
        }

        if ($assignment->getMaxScore() <= 0 ) {
            http_response_code(400);
            return ['ok'=> false, 'error'=> $assignment->getMaxScore() < 0 ? 'El puntaje no puede ser negativo' : 'El puntaje tiene que ser mayor a 0'];
        }

        if ($file !== null) {
            if (!AssignmentLogic::checkFileToUpload($file)['ok']) {
                http_response_code(400);
                return AssignmentLogic::checkFileToUpload($file);
            }

            $size = $file['size'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            $origName = $file['name'];
            $extention = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

            $year = date('Y');
            $month = date('m');
            $uploadDirBase = AssignmentLogic::FILE_PATH;
            $targetDir = sprintf("%s/%s/%s", $uploadDirBase, $year, $month);

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0750, true);
            }

            // Random name + extension
            $storageName = bin2hex(random_bytes(16)) . '.' . $extention;
            $targetPath = $targetDir . '/' . $storageName;

            $fileObject = new File($origName, $storageName, $mime, $extention, $size);
        
            $created = $assignmentPersistence->createAssignmentWithFile($assignment, $fileObject, $teacherId);

            if (!$created) {
                http_response_code(500);
                return ['ok' => false, 'message' => 'No se pudo crear la tarea'];
            }
            
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                http_response_code(500);
                return ['ok' => false, 'error' => 'Fallo al mover el archivo'];
            }
        
            http_response_code(200);
            return ['ok' => true, 'message' => 'La tarea fué creada con éxito'];
        }

        $created = $assignmentPersistence->createAssignment($assignment, $teacherId);

        if (!$created) {
            http_response_code(500);
            return ['ok' => false, 'message' => 'No se pudo crear la tarea'];
        } else {
            http_response_code(200);
            return ['ok' => true, 'message' => 'La tarea fué creada con éxito'];
        }

    }

    private static function checkFileToUpload(array $file): array {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Error al subir archivo '.$file['error']];
        }

        $size = $file['size'];
        if ($size > AssignmentLogic::MAX_SIZE) {
            return ['ok' => false, 'error' => 'El archivo es muy pesado'];
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, AssignmentLogic::ALLOWED_MIME)) {
            return ['ok' => false, 'error' => 'Tipo de archivo inválido'];
        }

        $origName = $file['name'];
        $extention = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (!in_array($extention, AssignmentLogic::ALLOWED_EXTENSIONS)) {
            return ['ok' => false, 'error' => 'Extension inválida'];
        }

        return ['ok' => true];
    }

    public function getAllAssignmentsCountAdmin() : int {
        AuthMiddleware::authorize(['admin']);

        $assignmentPersistence = AssignmentPersistenceFacade::getInstance()->getIAssignmentPersistence();

        $assignmentsCount = $assignmentPersistence->getAllAssignmentsCountAdmin();
    
        return $assignmentsCount;
    }

    public function getAllTurnedInAssignmentsCountAdmin() : int {
        AuthMiddleware::authorize(['admin']);

        $assignmentPersistence = AssignmentPersistenceFacade::getInstance()->getIAssignmentPersistence();

        $turnedInAssignmentsCount = $assignmentPersistence->getAllTurnedInAssignmentsCountAdmin();
    
        return $turnedInAssignmentsCount;
    }

    private static function needAuthentication(): bool {
        $user = AuthMiddleware::authenticate();

        return $user !== null;
    }

}

?>