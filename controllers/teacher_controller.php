<?php
require_once __DIR__.'/../models/teacher_model.php';
function teacher_controller_teacher(): array {
        $u=role('teacher');
        $t=teacher_byUser((int)$u['id']);
        if(!$t) {
            http_response_code(403);
            exit('Teacher profile not found.');
        }
        return $t;
    }
function teacher_controller_dashboard() {
        $t=teacher_controller_teacher();
        $classes=teacher_classes((int)$t['id']);
        $title='Teacher Dashboard';
        $view=__DIR__.'/../views/teacher/dashboard.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function teacher_controller_background() {
        $t=teacher_controller_teacher();
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $bg=trim((string)($_POST['background']??''));
            $ok=teacher_saveBackground((int)$t['id'],$bg);
            flash($ok?'Background updated.':'Unable to update background.');
            redirect('teacher/background');
        }
        $title='Teacher Background';
        $view=__DIR__.'/../views/teacher/background.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function teacher_controller_attendance() {
        $t=teacher_controller_teacher();
        $tid=(int)$t['id'];
        $classes=teacher_classes($tid);
        $students=[];
        $cid=(int)($_GET['class_id']??$_POST['class_id']??0);
        $date=(string)($_GET['date']??$_POST['date']??date('Y-m-d'));
        if(!valid_date($date)) $date=date('Y-m-d');
        $existing=[];
        if($cid && teacher_ownsClass($tid,$cid)) {
            $students=teacher_students($cid);
            $existing=teacher_attendanceForDate($tid,$cid,$date);
} else $cid=0;
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $date=trim((string)($_POST['date']??''));
            $statuses=$_POST['status']??[];
            $ok=$cid>0 && teacher_saveAttendance($tid,$cid,$date,is_array($statuses)?$statuses:[]);
            flash($ok?'Attendance saved/updated.':'Unable to save attendance. Check the class and date.');
            header('Location: index.php?page=teacher/attendance&class_id='.$cid.'&date='.urlencode($date).'&from='.urlencode($date).'&to='.urlencode($date));
            exit;
        }
        $from=$_GET['from']??date('Y-m-01');
        $to=$_GET['to']??date('Y-m-d');
        if(!valid_date((string)$from)) $from=date('Y-m-01');
        if(!valid_date((string)$to)) $to=date('Y-m-d');
        $records=$cid?teacher_attendanceRange($tid,$cid,$from,$to):[];
        $title='Attendance & Range';
        $view=__DIR__.'/../views/teacher/attendance.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function teacher_controller_marks() {
        $t=teacher_controller_teacher();
        $tid=(int)$t['id'];
        $classes=teacher_classes($tid);
        $students=[];
        $subjects=[];
        $cid=(int)($_GET['class_id']??$_POST['class_id']??0);
        if($cid && teacher_ownsClass($tid,$cid)) {
            $students=teacher_students($cid);
            $subjects=teacher_subjects($cid);
} else $cid=0;
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            if(($_POST['action']??'')==='add_subject') {
                $name=trim((string)($_POST['subject_name']??''));
                $ok=$cid>0 && teacher_addSubject($tid,$cid,$name);
                flash($ok?'Subject added successfully. You can now enter marks for it.':'Unable to add subject. Check the subject name or duplicate subject.');
                header('Location: index.php?page=teacher/marks&class_id='.$cid);
                exit;
            }
            $mid=(int)($_POST['mark_id']??0);
            $sid=(int)($_POST['student_id']??0);
            $sub=(int)($_POST['subject_id']??0);
            $exam=trim((string)($_POST['exam']??''));
            $marks=(float)($_POST['marks']??-1);
            $ok=$mid>0 ? teacher_updateMark($tid,$mid,$sid,$sub,$exam,$marks) : teacher_saveMark($tid,$sid,$sub,$exam,$marks);
            flash($ok?($mid?'Result updated successfully.':'Result saved successfully.'):'Unable to save result. Check the student, subject, exam and mark range (0-100).');
            header('Location: index.php?page=teacher/marks&class_id='.$cid);
            exit;
        }
        $records=$cid?teacher_marks($tid,$cid):[];
        $title='Enter / Update Marks';
        $view=__DIR__.'/../views/teacher/marks.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function teacher_controller_availability() {
        $t=teacher_controller_teacher();
        $tid=(int)$t['id'];
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $day=trim((string)($_POST['day']??''));
            $from=trim((string)($_POST['from_time']??''));
            $to=trim((string)($_POST['to_time']??''));
            $ok=teacher_saveAvailability($tid,$day,$from,$to);
            flash($ok?'Availability saved.':'Invalid time or duplicate availability.');
            redirect('teacher/availability');
        }
        $availability=teacher_availability($tid);
        $title='Availability';
        $view=__DIR__.'/../views/teacher/availability.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function teacher_controller_deleteAvailability() {
        $t=teacher_controller_teacher();
        check_csrf();
        $id=(int)($_POST['id']??0);
        $ok=$id>0 && teacher_deleteAvailability((int)$t['id'],$id);
        json_response($ok,$ok?'Availability removed.':'Unable to remove availability.');
    }

