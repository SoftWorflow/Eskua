<?php

require_once(__DIR__ . "/../middleware/auth.php");
require_once(__DIR__ . "/../../../backend/db_connect.php");
header('Content-Type: application/json');

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!$data && !isset($data['id'])) exit;

$userId = $data['id'];

$auth = new AuthMiddleware();
$auth->authorize(['admin']);

$dbConnection = new db_connect();
$conn = $dbConnection->connect();

$stmt = $conn->prepare("select profile_picture_url as profile_pic, id, username, display_name, email, role from `users` where id = ?;");
$stmt->execute([$userId]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

echo json_encode($userData);

?>