<?php

require_once(__DIR__ . "/../middleware/auth.php");
require_once(__DIR__ . "/../../../backend/db_connect.php");
header('Content-Type: application/json');

$auth = new AuthMiddleware();
$auth->authorize(['admin']);

$dbConnection = new db_connect();
$conn = $dbConnection->connect();

$sql = "select g.id, u.display_name as teacher, g.name as level from `groups` as g join `users` as u on g.teacher = u.id;";

$stmt = $conn->prepare($sql);
$stmt->execute();
$groupsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt->closeCursor();

echo json_encode($groupsData);

?>