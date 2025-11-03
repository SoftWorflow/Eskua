<?php

require_once(__DIR__ . "/../middleware/auth.php");
require_once(__DIR__ . "/../../../backend/db_connect.php");
header('Content-Type: application/json');

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!$data && !isset($data['id'])) exit;

$groupId = $data['id'];

$auth = new AuthMiddleware();
$auth->authorize(['admin']);

$dbConnection = new db_connect();
$conn = $dbConnection->connect();

$stmt = $conn->prepare("select g.id as id, u.display_name as teacher, g.name as level, g.code as code from `groups` as g join `users` as u on g.teacher = u.id where g.id = ?;");
$stmt->execute([$groupId]);
$groupData = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

$stmt = $conn->prepare("select count(*) as total_members from `students` where `group` = ?;");
$stmt->execute([$groupId]);
$groupData['members'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_members'];
$stmt->closeCursor();

$stmt = $conn->prepare("select count(*) as total_assignments from `assigned_assignments` where `group` = ?;");
$stmt->execute([$groupId]);
$groupData['assignments'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_assignments'];
$stmt->closeCursor();

echo json_encode($groupData);

?>