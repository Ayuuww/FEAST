<?php
session_start();

$role = $_SESSION['role'] ?? 'guest';

// Define navigation items for each role
$navItems = [
    'superadmin' => [
        ['name' => 'Dashboard', 'link' => 'superadmin-dashboard.php'],
        ['name' => 'Individual Report', 'link' => 'superadmin-individualreport.php'],
        ['name' => 'Acknowledgement Report', 'link' => 'superadmin-acknowledgementreport.php'],
        ['name' => 'Past Record', 'link' => 'superadmin-pastrecords.php'],
        ['name' => 'Evaluation Setting', 'link' => 'superadmin-evaluationsetting.php'],
        ['name' => 'Evaluation On/Off', 'link' => 'superadmin-evaluationswitch.php'],
        ['name' => 'Manage', 'link' => 'superadmin-addsmanagement.php'],
        ['name' => 'Faculty List', 'link' => 'superadmin-facultylist.php'],
        ['name' => 'Add New Faculty', 'link' => 'superadmin-facultycreation.php'],
        ['name' => 'Student List', 'link' => 'superadmin-studentlist.php'],
        ['name' => 'Add New Student', 'link' => 'superadmin-studentcreation.php'],
        ['name' => 'Admin List', 'link' => 'superadmin-adminlist.php'],
        ['name' => 'Add New Admin', 'link' => 'superadmin-admincreation.php'],
        ['name' => 'SuperAdmin List', 'link' => 'superadmin-superadminlist.php'],
        ['name' => 'Add New SuperAdmin', 'link' => 'superadmin-superadmincreation.php'],
        ['name' => 'Profile', 'link' => 'superadmin-user-profile.php'],
        ['name' => 'Sign Out', 'link' => 'logout.php'],
    ],
    'admin' => [
        ['name' => 'Dashboard', 'link' => 'admin-dashboard.php'],
        ['name' => 'Evaluate Form', 'link' => 'admin-evaluate.php'],
        ['name' => 'Evaluated Faculty', 'link' => 'admin-evaluatedfaculty.php'],
        ['name' => 'Subject List', 'link' => 'admin-subjectlist.php'],
        ['name' => 'Add Subject', 'link' => 'admin-subjectadding.php'],
        ['name' => 'Assign Subject', 'link' => 'admin-studentsubject.php'],
        ['name' => 'Subject Evaluated', 'link' => 'admin-evaluatedsubject.php'],
        ['name' => 'Individual Report', 'link' => 'admin-individualreport.php'],
        ['name' => 'Acknowledgement Report', 'link' => 'admin-acknowledgementreport.php'],
        ['name' => 'Overall Report SET', 'link' => 'admin-overallreport-set.php'],
        ['name' => 'Overall Report SEF', 'link' => 'admin-overallreport-sef.php'],
        ['name' => 'Overall Report (SET & SEF)', 'link' => 'admin-overallreport.php'],
        ['name' => 'Past Record', 'link' => 'admin-pastrecords.php'],
        ['name' => 'Profile', 'link' => 'admin-user-profile.php'],
        ['name' => 'Sign Out', 'link' => 'logout.php'],
    ],
    'faculty' => [
        ['name' => 'Dashboard', 'link' => 'faculty-dashboard.php'],
        ['name' => 'Subject', 'link' => 'faculty-evaluatedsubject.php'],
        ['name' => 'Records', 'link' => 'faculty-pastrecords.php'],
        ['name' => 'Profile', 'link' => 'faculty-user-profile.php'],
        ['name' => 'Sign Out', 'link' => 'logout.php'],
    ],
    'student' => [
        ['name' => 'Dashboard', 'link' => 'student-dashboard.php'],
        ['name' => 'Evaluate Form', 'link' => 'student-evaluate.php'],
        ['name' => 'Evaluated Subject', 'link' => 'student-evaluatedsubject.php'],
        ['name' => 'Profile', 'link' => 'student-user-profile.php'],
        ['name' => 'Sign Out', 'link' => 'logout.php'],
    ]
];

// Handle search query
$query = strtolower(trim($_GET['query'] ?? ''));
$suggestions = [];

if ($query !== '' && isset($navItems[$role])) {
    foreach ($navItems[$role] as $item) {
        if (strpos(strtolower($item['name']), $query) !== false) {
            $suggestions[] = $item;
        }
    }
}

header('Content-Type: application/json');
echo json_encode($suggestions);
