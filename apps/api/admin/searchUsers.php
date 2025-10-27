<?php

require_once(__DIR__ . "/../middleware/auth.php");
require_once(__DIR__ . "/../../../backend/db_connect.php");
header('Content-Type: application/json');

$auth = new AuthMiddleware();
$auth->authorize(['admin']);

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!$data && !isset($data['username'])) exit;

$username = $data['username'];

$dbConnection = new db_connect();
$conn = $dbConnection->connect();

$stmt = $conn->prepare("select id, username, role from `users` where username like ?;");
$stmt->execute(["%" . $username . "%"]);
$usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt->closeCursor();

if (empty($usersData)) {
    echo json_encode(['ok' => false, 'message' => 'No se encontraron resultados']);
} else {
    echo json_encode(['ok' => true, $usersData]);
}

?>