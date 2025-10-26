<?php

require_once(__DIR__ . "/../middleware/auth.php");
require_once(__DIR__ . "/../../../backend/db_connect.php");
header('Content-Type: application/json');

$auth = new AuthMiddleware();
$auth->authorize(['admin']);

$dbConnection = new db_connect();
$conn = $dbConnection->connect();

$stmt = $conn->prepare("select id, username, role from `users`;");
$stmt->execute();
$usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt->closeCursor();

echo json_encode($usersData);

?>