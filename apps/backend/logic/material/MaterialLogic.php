<?php

require_once(__DIR__ . "/../../middleware/auth.php");

require_once("IMaterialLogic.php");
require_once(__DIR__ . "/../../persistence/material/MaterialPersistenceFacade.php");

class MaterialLogic implements IMaterialLogic {

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

    public function uploadMaterial(PublicMaterial $publicMaterial, ?array $file): array {
        AuthMiddleware::authorize(['admin']);

        $uploaderId = AuthMiddleware::authenticate()['user_id'];

        if (!isset($publicMaterial) || !isset($file) || $uploaderId === null) {
            return ['ok' => false, 'error' => 'Datos incompletos para crear el material'];
        }

        if (empty($publicMaterial->getTitle())) {
            return ['ok' => false, 'error' => 'El titulo del material es obligatorio'];
        }

        $checkFile = $this::checkFileToUpload($file);
        if (!$checkFile['ok']) {
            return $checkFile;
        }

        $year = date('Y');
        $month = date('m');
        $uploadDirBase = $this::FILE_PATH;
        $targetDir = sprintf("%s/%s/%s", $uploadDirBase, $year, $month);

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0750, true);
        }

        $size = $file['size'];

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        $origName = $file['name'];
        $extention = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        $year = date('Y');
        $month = date('m');
        $uploadDirBase = MaterialLogic::FILE_PATH;
        $targetDir = sprintf("%s/%s/%s", $uploadDirBase, $year, $month);

        // Random name + extension
        $storageName = bin2hex(random_bytes(16)) . '.' . $extention;
        $targetPath = $targetDir . '/' . $storageName;

        $fileObject = new File($origName, $storageName, $mime, $extention, $size);

        $materialPersistence = MaterialPersistenceFacade::getInstance()->getIMaterialPersistence();
        $result = $materialPersistence->uploadMaterial($publicMaterial, $fileObject, $uploaderId);

        if (!$result) {
            return ['ok' => false, 'error' => 'Hubo un error al crear el material'];
        }

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['ok' => false, 'error' => 'Fallo al mover el archivo'];
        }

        return ['ok' => true, 'message' => 'Material creado exitosamente'];
    }

    public function getAllMaterials() : ?array {
        MaterialLogic::needAuthentication();

        $materialPersistence = MaterialPersistenceFacade::getInstance()->getIMaterialPersistence();

        $materials = $materialPersistence->getAllMaterials();

        if ($materials === null) {
            return ['ok' => false, 'error' => 'Hubo un error al obtener los materiales públicos'];
        }

        if (count($materials) !== 0) {
            foreach ($materials as &$material) {
                if (isset($material['createdDate']) && !empty($material['createdDate'])) {
                    $date = DateTime::createFromFormat('Y-m-d H:i:s', $material['createdDate']);
                    if ($date) {
                        $material['createdDate'] = $date->format('d-m-Y');
                    }
                }
            }
            unset($material);
        }

        return ['ok' => true, 'materials' => $materials];
    }

    public function getAllMaterialsAdmin(): array {
        AuthMiddleware::authorize(['admin']);

        $materialPersistence = MaterialPersistenceFacade::getInstance()->getIMaterialPersistence();
        
        $materialsData = $materialPersistence->getAllMaterialsAdmin();

        if (!$materialsData) {
            $materialsData['ok'] = false;
        } else {
            $materialsData['ok'] = true;
        }

        return $materialsData;
    }

    public function getMaterial(int $materialId) : ?array {
        MaterialLogic::needAuthentication();

        if (empty($materialId)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'No se ricibió el identificador del material']);
            exit;
        }

        if ($materialId === null) return null;

        $materialPersistence = MaterialPersistenceFacade::getInstance()->getIMaterialPersistence();

        $material = $materialPersistence->getMaterial($materialId);

        if (isset($material['createdDate']) && !empty($material['createdDate'])) {
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $material['createdDate']);
            if ($date) {
                $material['createdDate'] = $date->format('d-m-Y');
            }
        }

        return $material;
    }

    public function deleteMaterial(int $materialId): array {
        AuthMiddleware::authorize(['admin']);
        
        if (empty($materialId) || $materialId === null) {
            return ['ok' => false, 'error' => 'No se recibió el identificador del material'];
        }

        $materialPersistence = MaterialPersistenceFacade::getInstance()->getIMaterialPersistence();

        if ($materialPersistence->deleteMaterial($materialId)) {
            return ['ok' => true, 'message' => 'Material borrado con exito!'];
        }

        $material = $materialPersistence->getMaterial($materialId);
        
        if (!$material) {
            return ['ok' => false, 'error' => 'Material no encontrado'];
        }

        $storageName = $material['storageName'];
        $uploadedDate = new DateTime($material['createdDate']);

        $year = $uploadedDate->format('Y');
        $month = $uploadedDate->format('m');

        $filePath = MaterialLogic::FILE_PATH . '/'.$year.'/'.$month.'/'.$storageName;
        if (!file_exists($filePath)) {
            return ['ok' => false, 'error' => 'No se encontró el archivo'];
        }

        if (!unlink($filePath)) {
            return ['ok' => false, 'error' => 'Hubo un error al eliminar el archivo adjunto al material'];
        }

        return ['ok' => false, 'error' => 'Hubo un error al borrar el material'];

    }

    public function getSpecificMaterial(int $materialId): array {
        AuthMiddleware::authorize(['admin']);
        
        if ($materialId === null) {
            return ['ok' => false, 'No se recibio el identificador del material'];
        }

        $materialPersistence = MaterialPersistenceFacade::getInstance()->getIMaterialPersistence();

        $materialData = $materialPersistence->getSpecificMaterial($materialId);

        if (empty($materialData)) {
            return ['ok' => false, 'error' => 'No se encontró el material'];
        }

        $uploadedDate = new DateTime($materialData[0]['uploadedDate']);

        $year = $uploadedDate->format('Y');
        $month = $uploadedDate->format('m');
        $day = $uploadedDate->format('d');

        $filePath = 'uploads/'.$year.'/'.$month.'/'.$materialData[1];

        $materialData[0]['filePath'] = $filePath;

        return ['ok' => true, $materialData[0]];
    }

    private static function checkFileToUpload(array $file): array {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Error al subir archivo '.$file['error']];
        }

        $size = $file['size'];
        if ($size > MaterialLogic::MAX_SIZE) {
            return ['ok' => false, 'error' => 'El archivo es muy pesado'];
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, MaterialLogic::ALLOWED_MIME)) {
            return ['ok' => false, 'error' => 'Tipo de archivo inválido'];
        }

        $origName = $file['name'];
        $extention = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (!in_array($extention, MaterialLogic::ALLOWED_EXTENSIONS)) {
            return ['ok' => false, 'error' => 'Extension inválida'];
        }

        return ['ok' => true];
    }

    public function searchMaterial(string $title) : array {
        
        $materialPersistence = MaterialPersistenceFacade::getInstance()->getIMaterialPersistence();

        $materials = $materialPersistence->searchMaterial($title);

        if (empty($materials)) {
            return ['ok' => false, 'message' => 'No se encontraron resultados'];
        }

        return ['ok' => true, $materials];

    }

    public function getMaterialsCountAdmin(): int {
        AuthMiddleware::authorize(['admin']);

        $materialPersistence = MaterialPersistenceFacade::getInstance()->getIMaterialPersistence();

        $count = $materialPersistence->getMaterialsCountAdmin();

        return $count;
    }

    public function getRecentMaterials(): array {
        AuthMiddleware::authorize(['admin']);

        $materialPersistence = MaterialPersistenceFacade::getInstance()->getIMaterialPersistence();

        $materials = $materialPersistence->getRecentMaterials();

        if (empty($materials)) {
            return ['ok' => false, 'message' => 'No se encontraron resultados'];
        }

        return $materials;
    }

    public function getFileByMaterialAdmin(int $materialId): ?array {
        AuthMiddleware::authorize(['admin']);

        if (empty($materialId) || $materialId === null) {
            return ['ok' => false, 'error' => 'No se recibió el identificador del material'];
        }

        $materialPersistence = MaterialPersistenceFacade::getInstance()->getIMaterialPersistence();

        $data = $materialPersistence->getFileByMaterialAdmin($materialId);

        if (empty($data)) {
            return ['ok' => false, 'error' => 'No se encontró el material o el archivo'];
        }

        $fileData = $data[0];
        $materialData = $data[1];

        $storageName = $materialData['storageName'];
        $uploadedDate = new DateTime($materialData['uploadedDate']);

        $year = $uploadedDate->format('Y');
        $month = $uploadedDate->format('m');

        $filePath = 'uploads/'.$year.'/'.$month.'/'.$storageName;

        $materialData['filePath'] = $filePath;

        $response = array_merge($materialData, $fileData);

        return $response;
    }

    public function modifyMaterial(int $materialId, string $materialTitle, string $materialDescription, bool $hasChangedFile, ?array $fileData, string $filePath) : array {
        AuthMiddleware::authorize(['admin']);

        $userId = AuthMiddleware::authenticate()['user_id'];

        $materialPersistence = MaterialPersistenceFacade::getInstance()->getIMaterialPersistence();

        if (empty($materialId) || empty($materialTitle) || $userId === null) {
            return ['ok' => false, 'error' => 'Datos incompletos para modificar el material'];
        }

        if ($hasChangedFile) {
            if (!isset($fileData)) {
                return ['ok' => false, 'error' => 'No se recibió ningún archivo'];
            }

            $checkFile = $this::checkFileToUpload($fileData);
            if (!$checkFile['ok']) {
                return $checkFile;
            }

            $year = date('Y');
            $month = date('m');
            $uploadDirBase = MaterialLogic::FILE_PATH;
            $targetDir = sprintf("%s/%s/%s", $uploadDirBase, $year, $month);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0750, true);
            }

            $filePath = ltrim($filePath, '/');
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
            $absPath = $docRoot . '/' . $filePath;
            if (file_exists($absPath)) {
                if (!unlink($absPath)) {
                    return ['ok'=> false, 'error'=> 'Hubo un error al remplazar el archivo'];
                }
            } else {
                return ['ok'=> false,'error'=> 'El archivo a eliminar no existe'];
            }

            $file = $fileData;

            $origName = $file['name'];
            $extention = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            $size = $file['size'];

            $storageName = bin2hex(random_bytes(16)) . '.' . $extention;
            $targetPath = $targetDir . '/' . $storageName;

            $fileId = $materialPersistence->getMaterialFileId($materialId);

            $materialPersistence->deleteMaterialFile($fileId);

            if (!$this->createMaterialFile($materialId, $storageName, $origName, $mime, $extention, $size, $userId)) {
                return ['ok' => false, 'error' => 'Hubo un error al modificar el archivo del material'];
            }

            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                return ['ok' => false, 'error' => 'Fallo al mover el archivo'];
            }
        }

        if (!$materialPersistence->modifyMaterial($materialId, $materialTitle, $materialDescription)) {
            return ['ok' => false, 'error' => 'Hubo un error al modificar el material'];
        }

        return ['ok' => true, 'message' => 'Material modificado exitosamente'];
        
    }

    public function createMaterialFile(int $materialId, string $storageName, string $originalName, string $mime, string $extension, int $size, int $uploaderId) : bool {
        $materialPersistence = MaterialPersistenceFacade::getInstance()->getIMaterialPersistence();

        $result = $materialPersistence->createMaterialFile($materialId, $storageName, $originalName, $mime, $extension, $size, $uploaderId);

        return $result;
    }

    public function getMaterialFileId(int $materialId) : ?array {
        AuthMiddleware::authenticate();
        
        if (empty($materialId) || $materialId === null) {
            return ['ok' => false, 'error' => 'No se recibió el identificador del material'];
        }

        $materialPersistence = MaterialPersistenceFacade::getInstance()->getIMaterialPersistence();

        $fileData = $materialPersistence->getMaterialFileId($materialId);

        if (empty($fileData)) {
            return ['ok' => false, 'error' => 'No se encontró el archivo del material'];
        }

        return ['ok' => true, $fileData];
    }

    private static function needAuthentication(): bool {
        $user = AuthMiddleware::authenticate();

        return $user !== null;
    }

}

?>