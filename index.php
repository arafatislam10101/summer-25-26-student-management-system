<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/helpers.php';

foreach (['model','user_model','admin_model','teacher_model','student_model','parent_model','message_model'] as $m) require_once __DIR__ . '/models/' . $m . '.php';
foreach (['auth_controller','dashboard_controller','admin_controller','teacher_controller','student_controller','parent_controller','message_controller','ajax_controller'] as $c) require_once __DIR__ . '/controllers/' . $c . '.php';

check_session_timeout();
$page = $_GET['page'] ?? 'login';
$routes = [
'login'=>'auth_controller_login','signup'=>'auth_controller_signup','signup/register'=>'auth_controller_register','login/authenticate'=>'auth_controller_authenticate','logout'=>'auth_controller_logout',
'dashboard'=>'dashboard_controller_index',
'admin/dashboard'=>'admin_controller_dashboard','admin/students'=>'admin_controller_students','admin/teachers'=>'admin_controller_teachers','admin/teacher-background'=>'admin_controller_teacherBackground','admin/classes'=>'admin_controller_classes','admin/notices'=>'admin_controller_notices','admin/users'=>'admin_controller_users','admin/requests'=>'admin_controller_requests','admin/feedback'=>'admin_controller_feedback','admin/at-risk'=>'admin_controller_atRisk','admin/delete-class'=>'admin_controller_deleteClass','admin/delete-subject'=>'admin_controller_deleteSubject',
'teacher/dashboard'=>'teacher_controller_dashboard','teacher/background'=>'teacher_controller_background','teacher/attendance'=>'teacher_controller_attendance','teacher/marks'=>'teacher_controller_marks','teacher/availability'=>'teacher_controller_availability','teacher/delete-availability'=>'teacher_controller_deleteAvailability',
'student/dashboard'=>'student_controller_dashboard','student/profile'=>'student_controller_profile','student/edit-profile'=>'student_controller_editProfile','student/update-profile'=>'student_controller_updateProfile','student/attendance'=>'student_controller_attendance','student/results'=>'student_controller_results','student/notices'=>'student_controller_notices','student/teacher-availability'=>'student_controller_teacherAvailability','student/leave'=>'student_controller_leave','student/online'=>'student_controller_online','student/edit-request'=>'student_controller_editRequest','student/update-request'=>'student_controller_updateRequest','student/delete-request'=>'student_controller_deleteRequest',
'parent/dashboard'=>'parent_controller_dashboard','parent/attendance'=>'parent_controller_attendance','parent/results'=>'parent_controller_results','parent/notices'=>'parent_controller_notices','parent/payment'=>'parent_controller_payment','parent/make-payment'=>'parent_controller_makePayment','parent/payment-slip'=>'parent_controller_printPayment','parent/requests'=>'parent_controller_requests','parent/rating'=>'parent_controller_rating','parent/at-risk'=>'parent_controller_atRisk',
'messages'=>'message_controller_index','messages/send'=>'message_controller_send','api/admin'=>'ajax_controller_handle'];
if (isset($routes[$page]) && function_exists($routes[$page])) call_user_func($routes[$page]); else { http_response_code(404); echo '404 Not Found'; }
if (isset($conn) && $conn) mysqli_close($conn);
?>
