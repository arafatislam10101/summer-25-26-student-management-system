<?php
require_once __DIR__.'/../models/student_model.php';

class StudentController {
    private Student $m;

    function __construct() {
        $this->m=new Student;
    }

    function dashboard() {
        $u=role('student');
        $student=$this->m->byUser($u['id']);
        $summary=$this->m->attendanceSummary($student['id']);
        $title='Student Dashboard';
        $view=__DIR__.'/../views/student/dashboard.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }

    function profile() {
        $u=role('student');
        $student=$this->m->byUser($u['id']);
        $title='My Profile';
        $view=__DIR__.'/../views/student/profile.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }

    function editProfile() {
        $u=role('student');
        $student=$this->m->byUser($u['id']);
        $title='Edit Profile';
        $view=__DIR__.'/../views/student/edit_profile.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }

    function updateProfile() {
        $u=role('student');

        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();

            $ok=$this->m->updateProfile(
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

    function attendance() {
        $u=role('student');
        $student=$this->m->byUser($u['id']);
        $from=$_GET['from']??'';
        $to=$_GET['to']??'';
        $records=$this->m->attendance($student['id'],$from,$to);
        $summary=$this->m->attendanceSummary($student['id']);
        $title='My Attendance';
        $view=__DIR__.'/../views/student/attendance.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }

    function results() {
        $u=role('student');
        $student=$this->m->byUser($u['id']);
        $records=$this->m->results($student['id']);
        $title='Test Results';
        $view=__DIR__.'/../views/student/results.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }

       function notices() {
        role('student');
        $notices=$this->m->notices();
        $title='Notices';
        $view=__DIR__.'/../views/student/notices.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }

    function teacherAvailability() {
        role('student');
        $availability=$this->m->teacherAvailability();
        $title='Teacher Availability';
        $view=__DIR__.'/../views/student/teacher_availability.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }

    function leave() {
        $u=role('student');
        $student=$this->m->byUser($u['id']);

        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();

            $ok=$this->m->leave(
                $student['id'],
                trim($_POST['reason']),
                $_POST['from_date'],
                $_POST['to_date']
            );

            flash($ok?'Leave request submitted.':'Invalid date range.');
            redirect('student/leave');
        }

        $requests=$this->m->requests($student['id']);
        $title='Leave Request';
        $view=__DIR__.'/../views/student/request_form.php';
        $kind='leave';

        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }

    function online() {
        $u=role('student');
        $student=$this->m->byUser($u['id']);

        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();

            $ok=$this->m->online(
                $student['id'],
                $_POST['request_type'],
                trim($_POST['reason'])
            );

            flash($ok?'Request submitted.':'Invalid request.');
            redirect('student/online');
        }

        $requests=$this->m->requests($student['id']);
        $title='Online / Offline Request';
        $view=__DIR__.'/../views/student/request_form.php';
        $kind='online';

        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }

    function editRequest() {

    $u=role('student');

    $id=(int)($_GET['id']??0);
    $type=$_GET['type']??'';


    $request=$this->m->requestById($id,$type);
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





function updateRequest() {

    $u=role('student');


    if($_SERVER['REQUEST_METHOD']==='POST') {

        check_csrf();


        $ok=$this->m->updateRequest(
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



function deleteRequest() {

    $u=role('student');


    $id=(int)($_GET['id']??0);
    $type=$_GET['type']??'';


    $ok=$this->m->deleteRequest($id,$type);


    flash($ok?'Request deleted.':'Delete failed.');

    redirect('student/online');

}

}