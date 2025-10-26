<?php

require_once(__DIR__ . "/../middleware/auth.php");
require_once(__DIR__ . "/../../../backend/db_connect.php");
header('Content-Type: application/json');

$auth = new AuthMiddleware();
$auth->authorize(['admin']);

$dbConnection = new db_connect();
$conn = $dbConnection->connect();

$stmt = $conn->prepare("select count(*) as total from `users`;");
$stmt->execute();
$totalUserCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("select count(*) as total from `guests`;");
$stmt->execute();
$guestCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("select count(*) as total from `students`");
$stmt->execute();
$studentCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("select count(*) as total from `teachers`;");
$stmt->execute();
$teacherCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("select count(*) as total from `admins`;");
$stmt->execute();
$adminCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$response = [
    'totalUsers' => $totalUserCount,
    'totalGuests' => $guestCount,
    'totalStudents' => $studentCount,
    'totalTeachers' => $teacherCount,
    'totalAdmins' => $adminCount
];

$stmt->closeCursor();

echo json_encode($response);

?>