<?php

require_once(__DIR__ . "/../../../backend/db_connect.php");
header('Content-Type: application/json');

$dbConnection = new db_connect();
$conn = $dbConnection->connect();

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM `users`;");
$stmt->execute();
$totalUserCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM `guests`;");
$stmt->execute();
$guestCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM `students`");
$stmt->execute();
$studentCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM `teachers`;");
$stmt->execute();
$teacherCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM `admins`;");
$stmt->execute();
$adminCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$response = [
    'totalUsers' => $totalUserCount,
    'totalGuests' => $guestCount,
    'totalStudents' => $studentCount,
    'totalTeachers' => $teacherCount,
    'totalAdmins' => $adminCount
];

echo json_encode($response);

?>