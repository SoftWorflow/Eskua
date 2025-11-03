<?php

require_once(__DIR__ . "/../middleware/auth.php");
require_once(__DIR__ . "/../../../backend/db_connect.php");
header('Content-Type: application/json');

$auth = new AuthMiddleware();
$auth->authorize(['admin']);

$dbConnection = new db_connect();
$conn = $dbConnection->connect();

// USERS
$stmt = $conn->prepare("select count(*) as total from `users`;");
$stmt->execute();
$totalUserCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// GUESTS
$stmt = $conn->prepare("select count(*) as total from `guests`;");
$stmt->execute();
$guestCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// STUDENTS
$stmt = $conn->prepare("select count(*) as total from `students`");
$stmt->execute();
$studentCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// TEACHERS
$stmt = $conn->prepare("select count(*) as total from `teachers`;");
$stmt->execute();
$teacherCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// ADMINS
$stmt = $conn->prepare("select count(*) as total from `admins`;");
$stmt->execute();
$adminCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// PUBLIC MATERIALS
$stmt = $conn->prepare("select count(*) as total from `public_materials`;");
$stmt->execute();
$publicMaterialsCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// GROUPS
$stmt = $conn->prepare("select count(*) as total from `groups`;");
$stmt->execute();
$groupsCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// ASSIGNMENTS
$stmt = $conn->prepare("select count(*) as total from `assigned_assignments`;");
$stmt->execute();
$assignmentsCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// TUREND IN ASSIGNMENTS
$stmt = $conn->prepare("select count(*) as total from `turned_in_assignments`;");
$stmt->execute();
$turnedInAssignmentsCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("select pm.title, f.extension as type, pm.uploaded_date as uploadedDate from public_materials as pm left join public_materials_files as pmf on pm.id = pmf.public_material left join files as f on pmf.file = f.id order by pm.uploaded_date desc limit 4;");
$stmt->execute();
$recentMaterials = $stmt->fetchAll(PDO::FETCH_ASSOC);

$response = [
    'totalUsers' => $totalUserCount,
    'totalGuests' => $guestCount,
    'totalStudents' => $studentCount,
    'totalTeachers' => $teacherCount,
    'totalAdmins' => $adminCount,
    'publicMaterialsCount' => $publicMaterialsCount,
    'groupsCount' => $groupsCount,
    'assignmentsCount' => $assignmentsCount,
    'turnedInAssignmentsCount' => $turnedInAssignmentsCount,
    'recentMaterials' => $recentMaterials
];

foreach ($response as $key => $value) {
    if ($value == 0) {
        $response[$key] = "N/A";
    }
}

$stmt->closeCursor();

echo json_encode($response);

?>