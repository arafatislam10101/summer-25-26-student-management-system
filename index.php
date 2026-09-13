<?php
// ================================================================
// FRONT CONTROLLER / ROUTER
// Every request enters through index.php?page=<route>
// Pattern follows the reference Library Management System.
// ================================================================

/* ================= 1. Load application ================= */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/helpers.php';


/* ================= 2. Load MODELS ================= */

require_once __DIR__ . '/models/model.php';
require_once __DIR__ . '/models/user_model.php';
require_once __DIR__ . '/models/admin_model.php';
require_once __DIR__ . '/models/teacher_model.php';
require_once __DIR__ . '/models/student_model.php';
require_once __DIR__ . '/models/parent_model.php';
require_once __DIR__ . '/models/message_model.php';


/* ================= 3. Load CONTROLLERS ================= */

require_once __DIR__ . '/controllers/auth_controller.php';
require_once __DIR__ . '/controllers/dashboard_controller.php';
require_once __DIR__ . '/controllers/admin_controller.php';
require_once __DIR__ . '/controllers/teacher_controller.php';
require_once __DIR__ . '/controllers/student_controller.php';
require_once __DIR__ . '/controllers/parent_controller.php';
require_once __DIR__ . '/controllers/message_controller.php';
require_once __DIR__ . '/controllers/ajax_controller.php';


/* ================= 4. Session timeout ================= */

check_session_timeout();


/* ================= 5. Route request ================= */

$page = $_GET['page'] ?? 'login';


switch ($page) {


    /* ---------- Public pages ---------- */

    case 'login':
    (new AuthController())->login();
    break;


    case 'signup':
    (new AuthController())->signup();
    break;


    case 'signup/register':
    (new AuthController())->register();
    break;


    case 'login/authenticate':
    (new AuthController())->authenticate();
    break;


    case 'logout':
    (new AuthController())->logout();
    break;



    /* ---------- Common dashboard ---------- */

    case 'dashboard':
    (new DashboardController())->index();
    break;



    /* ---------- Admin ---------- */

    case 'admin/dashboard':
    (new AdminController())->dashboard();
    break;


    case 'admin/students':
    (new AdminController())->students();
    break;


    case 'admin/teachers':
    (new AdminController())->teachers();
    break;


    case 'admin/teacher-background':
    (new AdminController())->teacherBackground();
    break;


    case 'admin/classes':
    (new AdminController())->classes();
    break;


    case 'admin/notices':
    (new AdminController())->notices();
    break;


    case 'admin/users':
    (new AdminController())->users();
    break;


    case 'admin/requests':
    (new AdminController())->requests();
    break;


    case 'admin/feedback':
    (new AdminController())->feedback();
    break;


    case 'admin/at-risk':
    (new AdminController())->atRisk();
    break;

   
    case 'admin/delete-class':
    (new AdminController())->deleteClass();
    break;


    case 'admin/delete-subject':
    (new AdminController())->deleteSubject();
    break;



    /* ---------- Teacher ---------- */

    case 'teacher/dashboard':
    (new TeacherController())->dashboard();
    break;


    case 'teacher/background':
    (new TeacherController())->background();
    break;


    case 'teacher/attendance':
    (new TeacherController())->attendance();
    break;


    case 'teacher/marks':
    (new TeacherController())->marks();
    break;


    case 'teacher/availability':
    (new TeacherController())->availability();
    break;


    case 'teacher/delete-availability':
    (new TeacherController())->deleteAvailability();
    break;



    /* ---------- Student ---------- */

    case 'student/dashboard':
    (new StudentController())->dashboard();
    break;


    case 'student/profile':
    (new StudentController())->profile();
    break;


    case 'student/edit-profile':
    (new StudentController())->editProfile();
    break;


    case 'student/update-profile':
    (new StudentController())->updateProfile();
    break;


    case 'student/attendance':
    (new StudentController())->attendance();
    break;


    case 'student/results':
    (new StudentController())->results();
    break;


    case 'student/notices':
    (new StudentController())->notices();
    break;


    case 'student/teacher-availability':
    (new StudentController())->teacherAvailability();
    break;


    case 'student/leave':
    (new StudentController())->leave();
    break;



    case 'student/online':
    (new StudentController())->online();
    break;

    case 'student/edit-request':
    (new StudentController())->editRequest();
    break;


    case 'student/update-request':
    (new StudentController())->updateRequest();
    break;


    case 'student/delete-request':
    (new StudentController())->deleteRequest();
    break;



    /* ---------- Parent ---------- */

    case 'parent/dashboard':
    (new ParentController())->dashboard();
    break;


    case 'parent/attendance':
    (new ParentController())->attendance();
    break;


    case 'parent/results':
    (new ParentController())->results();
    break;


    case 'parent/notices':
    (new ParentController())->notices();
    break;


    case 'parent/payment':
    (new ParentController())->payment();
    break;


    case 'parent/make-payment':
    (new ParentController())->makePayment();
    break;


    case 'parent/payment-slip':
    (new ParentController())->printPayment();
    break;


    case 'parent/requests':
    (new ParentController())->requests();
    break;


    case 'parent/rating':
    (new ParentController())->rating();
    break;


    case 'parent/at-risk':
    (new ParentController())->atRisk();
    break;



    /* ---------- Messaging ---------- */

    case 'messages':
    (new MessageController())->index();
    break;


    case 'messages/send':
    (new MessageController())->send();
    break;



    /* ---------- AJAX / JSON ---------- */

    case 'api/admin':
    (new AjaxController())->handle();
    break;



    /* ---------- Unknown route ---------- */

    default:
    http_response_code(404);
    echo '404 Not Found';
    break;
}


if (isset($conn) && $conn instanceof mysqli) {
    mysqli_close($conn);
}