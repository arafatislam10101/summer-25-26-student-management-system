<?php
$navUser = current_user();
$route = $_GET['page'] ?? 'dashboard';
$role = $navUser['role'] ?? '';
$avatar = 'assets/images/profiles/' . ($role ?: 'student') . '.svg';

/* Keep one clean navigation system for every role.  The labels and routes are
   role-aware, but the visual structure stays identical for Admin/Teacher/
   Student/Parent so the portal feels like one product. */
$navs = [
 'admin'=>[
  'Overview'=>[['admin/dashboard','dashboard','Dashboard']],
  'Management'=>[['admin/students','students','Students'],['admin/teachers','teachers','Teachers'],['admin/teacher-background','profile','Teacher Background'],['admin/classes','classes','Classes & Subjects'],['admin/users','users','User Accounts']],
  'Operations'=>[['admin/notices','notice','Notices'],['admin/requests','requests','Requests'],['admin/at-risk','risk','At-Risk Students'],['admin/feedback','feedback','Feedback']],
 ],
 'teacher'=>[
  'Overview'=>[['teacher/dashboard','dashboard','Dashboard']],
  'Teaching'=>[['teacher/attendance','attendance','Attendance & Range'],['teacher/marks','marks','Marks'],['teacher/availability','availability','Availability']],
  'Profile'=>[['teacher/background','profile','Teacher Background']],
 ],
 'student'=>[
  'Overview'=>[['student/dashboard','dashboard','Dashboard']],
  'My Study'=>[['student/profile','profile','My Profile'],['student/attendance','attendance','Attendance'],['student/results','results','Test Results'],['student/notices','notice','Notices'],['student/teacher-availability','availability','Teacher Availability']],
  'Requests'=>[['student/online','online','Online / Offline'],['student/leave','leave','Leave Request']],
 ],
 'parent'=>[
  'Overview'=>[['parent/dashboard','dashboard','Dashboard']],
  'Child Overview'=>[['parent/attendance','attendance','Child Attendance'],['parent/results','results','Results & Performance'],['parent/at-risk','risk','At-Risk Alerts'],['parent/notices','notice','Notices & Accounts']],
  'Services'=>[['parent/payment','payment','Payment System'],['parent/requests','requests','Package Request'],['parent/rating','rating','Rating & Feedback']],
 ],
];

function sidebar_icon(string $name): string {
    $icons = [
        'dashboard'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<rect x="3" y="3" width="7" height="7" rx="1.5"/>
<rect x="14" y="3" width="7" height="7" rx="1.5"/>
<rect x="3" y="14" width="7" height="7" rx="1.5"/>
<rect x="14" y="14" width="7" height="7" rx="1.5"/>
</svg>',
        'students'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<circle cx="9" cy="8" r="3"/>
<path d="M3.5 19a5.5 5.5 0 0 1 11 0"/>
<path d="M16 11a3 3 0 1 0-1.2-5.75"/>
<path d="M17 14.5a5 5 0 0 1 4 4.5"/>
</svg>',
        'teachers'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<path d="M4 6.5h16v11H4z"/>
<path d="M8 6.5V4h8v2.5M8 11h8M9 15h6"/>
</svg>',
        'classes'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<path d="M4 5.5h16v14H4z"/>
<path d="M8 5.5V3M16 5.5V3M4 9h16M8 13h2M12 13h2M16 13h2M8 17h2M12 17h2"/>
</svg>',
        'users'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<circle cx="8" cy="8" r="3"/>
<circle cx="17" cy="9" r="2.5"/>
<path d="M2.5 20a5.5 5.5 0 0 1 11 0M14 19a4.5 4.5 0 0 1 7.5-1.5"/>
</svg>',
        'notice'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>
</svg>',
        'requests'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<path d="M6 3h12v18H6z"/>
<path d="M9 7h6M9 11h6M9 15h4"/>
</svg>',
        'risk'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<path d="M12 3 21 20H3z"/>
<path d="M12 9v5M12 17h.01"/>
</svg>',
        'feedback'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<path d="M20 11.5a7.5 7.5 0 0 1-7.5 7.5H8l-4 2v-5.2A7.5 7.5 0 1 1 20 11.5z"/>
</svg>',
        'attendance'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<rect x="4" y="5" width="16" height="15" rx="2"/>
<path d="M8 3v4M16 3v4M4 9h16M8 13h2M12 13h2M8 16h2"/>
</svg>',
        'marks'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<path d="m5 19 3.5-.8L19 7.7 16.3 5 5.8 15.5z"/>
<path d="m14.8 6.5 2.7 2.7"/>
</svg>',
        'availability'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<circle cx="12" cy="12" r="8.5"/>
<path d="M12 7v5l3.5 2"/>
</svg>',
        'profile'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<circle cx="12" cy="8" r="3.5"/>
<path d="M5 20a7 7 0 0 1 14 0"/>
</svg>',
        'results'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<path d="M4 19V5M4 19h16"/>
<path d="m7 15 4-4 3 2 5-6"/>
</svg>',
        'online'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<circle cx="12" cy="12" r="8"/>
<path d="M8 12h8M12 8v8"/>
</svg>',
        'leave'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<path d="M6 3h12v18H6z"/>
<path d="M9 7h6M9 11h4"/>
<path d="m14 16 2 2 4-4"/>
</svg>',
        'payment'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<rect x="3" y="5" width="18" height="14" rx="2"/>
<path d="M3 9h18M7 14h4"/>
</svg>',
        'rating'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9z"/>
</svg>',
        'message'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<path d="M20 11.5a7.5 7.5 0 0 1-7.5 7.5H8l-4 2v-5.2A7.5 7.5 0 1 1 20 11.5z"/>
</svg>',
        'logout'=>'<svg viewBox="0 0 24 24" aria-hidden="true">
<path d="M10 5H5v14h5M14 8l4 4-4 4M9 12h9"/>
</svg>',
    ];
    return $icons[$name] ?? $icons['dashboard'];
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#0b1220">
<title>
<?=esc($title ?? 'Student Management System')?>
</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="portal-body">
<?php if (!$navUser): ?>

<?php else: ?>
<div class="app-shell">
<aside class="sidebar" id="sidebar">
<div class="sidebar-inner">
<a class="brand" href="index.php?page=dashboard" aria-label="EduManage home">
<span class="brand-mark" aria-hidden="true">
<span>ED</span>
</span>
<span class="brand-copy">
<strong>EduManage</strong>
<small>Student Management</small>
</span>
</a>
<a class="profile-mini" href="index.php?page=dashboard">
<span class="profile-avatar-wrap">
<img src="<?=esc($avatar)?>" alt="Profile photo">
<i>
</i>
</span>
<span class="profile-copy">
<strong>
<?=esc($navUser['name'])?>
</strong>
<small>
<?=esc(ucfirst($role))?> Account</small>
</span>
<span class="profile-arrow" aria-hidden="true">›</span>
</a>
<div class="sidebar-scroll">
<?php foreach(($navs[$role] ?? []) as $section=>$items): ?>
<div class="sidebar-label">
<?=esc($section)?>
</div>
<nav class="side-nav" aria-label="<?=esc($section)?> navigation">
<?php foreach($items as [$r,$icon,$label]): ?>

<a class="<?=($route===$r?'active':'')?>" href="index.php?page=<?=esc($r)?>">
<span class="nav-icon">
<?=sidebar_icon($icon)?>
</span>
<span class="nav-label">
<?=esc($label)?>
</span>
<?php if ($route===$r): ?>

<span class="active-dot" aria-hidden="true">
</span>
<?php endif; ?>

</a>
<?php endforeach; ?>

</nav>
<?php endforeach; ?>

<div class="sidebar-label communication-label">Communication</div>
<nav class="side-nav">
<a class="<?=($route==='messages'?'active':'')?>" href="index.php?page=messages">
<span class="nav-icon">
<?=sidebar_icon('message')?>
</span>
<span class="nav-label">Messaging</span>
<?php if ($route==='messages'): ?>

<span class="active-dot" aria-hidden="true">
</span>
<?php endif; ?>

</a>
</nav>
</div>
<div class="sidebar-footer">
<div class="sidebar-footer-note">EduManage Portal</div>
</div>
</div>
</aside>
<section class="main-area">
<header class="topbar">
<div class="top-actions">
<button class="mobile-menu" id="mobileMenu" type="button" aria-label="Open navigation" aria-controls="sidebar" aria-expanded="false">☰</button>
<div class="page-context">
<small>Student Management System</small>
<div class="page-context-title">
<h1>
<?=esc($title ?? 'Dashboard')?>
</h1>
</div>
</div>
</div>
<div class="top-actions">
<a class="top-profile" href="index.php?page=dashboard">
<img src="<?=esc($avatar)?>" alt="Profile photo">
<span>
<?=esc($navUser['name'])?>
</span>
</a>
<a class="top-signout-btn" href="index.php?page=logout" title="Sign out">
<span>
<?=sidebar_icon('logout')?>
</span>
<strong>Sign Out</strong>
</a>
</div>
</header>
<main>
<?php if ($m=flash()): ?>

<div class="alert" data-auto-hide>
<?=esc($m)?>
</div>
<?php endif; ?>

<?php endif; ?>

