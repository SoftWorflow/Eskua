<?php

require_once(__DIR__ . "/../middleware/auth.php");

require_once(__DIR__ . "/../../../backend/DTO/Users/User.php");
require_once(__DIR__ . "/../../../backend/logic/user/UserLogicFacade.php");
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$auth = new AuthMiddleware();
$auth::authorize(['teacher', 'student']);

$userLogic = UserLogicFacade::getInstance()->getIUserLogic();

$groupId = $input['id'];

$assignments = $userLogic->getAssignmentsFromGroup($groupId);

$currentDate = new DateTime('now');

$responseAssignments = [];
if ($assignments !== null) {
    foreach ($assignments as $assignment) {
        $dueDate = new DateTime($assignment['dueDate']);
    
        if ($dueDate > $currentDate) {
            $responseAssignments[] = $assignment;
        }
    }
}

echo json_encode(['ok' => true, $assignments]);

?>