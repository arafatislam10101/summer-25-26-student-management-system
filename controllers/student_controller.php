<?php
require_once __DIR__.'/../models/student_model.php';
function student_controller_dashboard() {
        $u=role('student');
        $student=student_byUser($u['id']);
        $summary=student_attendanceSummary($student['id']);
        $title='Student Dashboard';
        $view=__DIR__.'/../views/student/dashboard.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function student_controller_profile() {
        $u=role('student');
        $student=student_byUser($u['id']);
        $title='My Profile';
        $view=__DIR__.'/../views/student/profile.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function student_controller_editProfile() {
        $u=role('student');
        $student=student_byUser($u['id']);
        $title='Edit Profile';
        $view=__DIR__.'/../views/student/edit_profile.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function student_controller_updateProfile() {
        $u=role('student');

        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();

            $ok=student_updateProfile(
                $u['id'],
                trim($_POST['name']),
                trim($_POST['email']),
                trim($_POST['phone']),
                trim($_POST['address'])
            );

            flash($ok?'Profile updated successfully.':'Profile update failed.');
            redirect('student/profile');
        }
    }
function student_controller_attendance() {
        $u=role('student');
        $student=student_byUser($u['id']);
        $from=$_GET['from']??'';
        $to=$_GET['to']??'';
        $records=student_attendance($student['id'],$from,$to);
        $summary=student_attendanceSummary($student['id']);
        $title='My Attendance';
        $view=__DIR__.'/../views/student/attendance.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function student_controller_results() {
        $u=role('student');
        $student=student_byUser($u['id']);
        $records=student_results($student['id']);
        $title='Test Results';
        $view=__DIR__.'/../views/student/results.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function student_controller_notices() {
        role('student');
        $notices=student_notices();
        $title='Notices';
        $view=__DIR__.'/../views/student/notices.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function student_controller_teacherAvailability() {
        role('student');
        $availability=student_teacherAvailability();
        $title='Teacher Availability';
        $view=__DIR__.'/../views/student/teacher_availability.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function student_controller_leave() {
        $u=role('student');
        $student=student_byUser($u['id']);

        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();

            $ok=student_leave(
                $student['id'],
                trim($_POST['reason']),
                $_POST['from_date'],
                $_POST['to_date']
            );

            flash($ok?'Leave request submitted.':'Invalid date range.');
            redirect('student/leave');
        }

        $requests=student_requests($student['id']);
        $title='Leave Request';
        $view=__DIR__.'/../views/student/request_form.php';
        $kind='leave';

        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function student_controller_online() {
        $u=role('student');
        $student=student_byUser($u['id']);

        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();

            $ok=student_online(
                $student['id'],
                $_POST['request_type'],
                trim($_POST['reason'])
            );

            flash($ok?'Request submitted.':'Invalid request.');
            redirect('student/online');
        }

        $requests=student_requests($student['id']);
        $title='Online / Offline Request';
        $view=__DIR__.'/../views/student/request_form.php';
        $kind='online';

        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function student_controller_editRequest() {

    $u=role('student');

    $id=(int)($_GET['id']??0);
    $type=$_GET['type']??'';


    $request=student_requestById($id,$type);
    $request['id']=$id;


    if(!$request) {
        flash('Request not found.');
        redirect('student/online');
    }


    $title='Edit Request';

    $view=__DIR__.'/../views/student/edit_request.php';


    include __DIR__.'/../views/partials/header.php';
    include $view;
    include __DIR__.'/../views/partials/footer.php';

}





function student_controller_updateRequest() {

    $u=role('student');


    if($_SERVER['REQUEST_METHOD']==='POST') {

        check_csrf();


        $ok=student_updateRequest(
            (int)$_POST['id'],
            $_POST['type'],
            trim($_POST['reason']),
            $_POST['from_date']??'',
            $_POST['to_date']??''
        );


        flash($ok?'Request updated.':'Update failed.');

        redirect('student/online');
    }

}



function student_controller_deleteRequest() {

    $u=role('student');


    $id=(int)($_GET['id']??0);
    $type=$_GET['type']??'';


    $ok=student_deleteRequest($id,$type);


    flash($ok?'Request deleted.':'Delete failed.');

    redirect('student/online');

}

