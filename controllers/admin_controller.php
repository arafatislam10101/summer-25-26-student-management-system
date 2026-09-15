<?php
require_once __DIR__.'/../models/admin_model.php';
require_once __DIR__.'/../models/user_model.php';
function admin_controller_dashboard() {
        role('admin');
        $stats=admin_stats();
        $title='Admin Dashboard';
        $view=__DIR__.'/../views/admin/dashboard.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function admin_controller_students() {
        role('admin');
        $edit=null;
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $r=null;
            if(isset($_POST['delete']))$r=[admin_deleteStudent((int)$_POST['delete']), 'Student deleted.'];
            elseif(isset($_POST['update']))$r=admin_updateStudent(['id'=>(int)$_POST['id'],'user_id'=>(int)$_POST['user_id'],'name'=>trim($_POST['name']),'email'=>trim($_POST['email']),'student_id'=>trim($_POST['student_id']),'phone'=>trim($_POST['phone']),'address'=>trim($_POST['address']),'class_id'=>(int)($_POST['class_id']??0),'parent_id'=>(int)($_POST['parent_id']??0)]);
            else$r=admin_createUserStudent(['name'=>trim($_POST['name']),'email'=>trim($_POST['email']),'password'=>$_POST['password'],'student_id'=>trim($_POST['student_id']),'phone'=>trim($_POST['phone']),'address'=>trim($_POST['address']),'class_id'=>(int)($_POST['class_id']??0),'parent_id'=>(int)($_POST['parent_id']??0)]);
            flash($r[1]);
            redirect('admin/students');
        }
        $students=admin_students();
        $classes=admin_classes();
        $parents=admin_parents();
        if(isset($_GET['edit']))$edit=admin_student((int)$_GET['edit']);
        $title='Manage Students';
        $view=__DIR__.'/../views/admin/students.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function admin_controller_teachers() {
        role('admin');
        $edit=null;
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            if(isset($_POST['delete']))$r=[admin_deleteTeacher((int)$_POST['delete']),'Teacher deleted.'];
            elseif(isset($_POST['update']))$r=admin_updateTeacher(['id'=>(int)$_POST['id'],'user_id'=>(int)$_POST['user_id'],'name'=>trim($_POST['name']),'email'=>trim($_POST['email']),'teacher_id'=>trim($_POST['teacher_id']),'phone'=>trim($_POST['phone']),'subject'=>trim($_POST['subject']),'background'=>trim($_POST['background'])]);
            else$r=admin_createUserTeacher(['name'=>trim($_POST['name']),'email'=>trim($_POST['email']),'password'=>$_POST['password'],'teacher_id'=>trim($_POST['teacher_id']),'phone'=>trim($_POST['phone']),'subject'=>trim($_POST['subject']),'background'=>trim($_POST['background'])]);
            flash($r[1]);
            redirect('admin/teachers');
        }
        $teachers=admin_teachers();
        $edit=isset($_GET['edit'])?admin_teacher((int)$_GET['edit']):null;
        $title='Manage Teachers';
        $view=__DIR__.'/../views/admin/teachers.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function admin_controller_teacherBackground() {
        role('admin');
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $teacherId=(int)($_POST['teacher_id']??0);
            $background=trim($_POST['background']??'');
            $ok=admin_updateTeacherBackground($teacherId,$background);
            flash($ok?'Teacher background updated.':'Unable to update teacher background.');
            redirect('admin/teacher-background&edit='.$teacherId);
        }
        $teachers=admin_teachers();
        $edit=null;
        if(isset($_GET['edit'])) $edit=admin_teacher((int)$_GET['edit']);
        $title='Teacher Background';
        $view=__DIR__.'/../views/admin/teacher_background.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function admin_controller_classes() {
        role('admin');
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $ok=false;
            if($_POST['entity']==='class') {
                $ok=admin_saveClassWithSubject(
                    (int)($_POST['id']??0),
                    trim($_POST['name']??''),
                    trim($_POST['section']??''),
                    (int)($_POST['teacher_id']??0),
                    (int)($_POST['subject_id']??0),
                    trim($_POST['subject_name']??'')
                );
            } else {
                $ok=admin_saveSubject((int)($_POST['id']??0),trim($_POST['subject_name']??''),(int)($_POST['class_id']??0));
            }
            flash($ok?'Saved successfully.':'Unable to save. Check duplicates.');
            redirect('admin/classes');
        }
        $classes=admin_classes();
        $subjects=admin_subjects();
        $teachers=admin_teachers();
        $editClass=null;
        $editSubject=null;
        $editSubjectForClass=null;
        $subjectsForClass=[];
        if(isset($_GET['edit_class'])) {
            foreach($classes as $c) if((int)$c['id']===(int)$_GET['edit_class']) {
                $editClass=$c;
                break;
            }
            if($editClass) {
                foreach($subjects as $s) {
                    if((int)($s['class_id']??0)===(int)$editClass['id']) {
                        $subjectsForClass[]=$s;
                        if($editSubjectForClass===null)$editSubjectForClass=$s;
                    }
                }
            }
        }
        if(isset($_GET['edit_subject'])) {
            foreach($subjects as $s) if((int)$s['id']===(int)$_GET['edit_subject']) {
                $editSubject=$s;
                break;
            }
            if($editSubject && !$editClass) {
                foreach($classes as $c) if((int)$c['id']===(int)($editSubject['class_id']??0)) {
                    $editClass=$c;
                    break;
                }
                if($editClass) {
                    foreach($subjects as $s) if((int)($s['class_id']??0)===(int)$editClass['id']) $subjectsForClass[]=$s;
                    $editSubjectForClass=$editSubject;
                }
            }
        }
        $title='Classes & Subjects';
        $view=__DIR__.'/../views/admin/classes.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function admin_controller_deleteClass() {
        role('admin');
        check_csrf();
        $ok=admin_deleteClass((int)$_POST['id']);
        json_response($ok,$ok?'Class deleted.':'Unable to delete class.');
    }
function admin_controller_deleteSubject() {
        role('admin');
        check_csrf();
        $ok=admin_deleteSubject((int)$_POST['id']);
        json_response($ok,$ok?'Subject deleted.':'Unable to delete subject.');
    }
function admin_controller_notices() {
        role('admin');
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $ok=isset($_POST['delete'])?admin_deleteNotice((int)$_POST['delete']):admin_saveNotice((int)($_POST['id']??0),trim($_POST['title']),trim($_POST['description']));
            flash($ok?'Notice saved.':'Unable to save notice.');
            redirect('admin/notices');
        }
        $notices=admin_notices();
        $edit=null;
        if(isset($_GET['edit']))foreach($notices as $n)if((int)$n['id']===(int)$_GET['edit'])$edit=$n;
        $title='Notices';
        $view=__DIR__.'/../views/admin/notices.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function admin_controller_users() {
        role('admin');
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            if((int)$_POST['id']!==$_SESSION['user']['id'])admin_toggle((int)$_POST['id']);
            redirect('admin/users');
        }
        $users=user_all();
        $title='User Accounts';
        $view=__DIR__.'/../views/admin/users.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function admin_controller_requests() {
        role('admin');
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            admin_setRequest($_POST['type'],(int)$_POST['id'],$_POST['status']);
            redirect('admin/requests');
        }
        $requests=admin_requests();
        $title='Request Management';
        $view=__DIR__.'/../views/admin/requests.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function admin_controller_atRisk() {
        role('admin');
        $atRisk=db_fetch_all("SELECT s.student_id,u.name,(SELECT ROUND(COALESCE(100*SUM(a.status IN (\'Present\',\'Late\'))/NULLIF(COUNT(*),0),0),2) FROM attendance a WHERE a.student_id=s.id) attendance_pct,(SELECT ROUND(COALESCE(AVG(m.marks),0),2) FROM marks m WHERE m.student_id=s.id) average_marks FROM students s JOIN users u ON u.id=s.user_id HAVING attendance_pct<60 OR average_marks<50 ORDER BY attendance_pct,average_marks");
        $title='At-Risk Students';
        $view=__DIR__.'/../views/admin/at_risk.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function admin_controller_feedback() {
        role('admin');
        $feedback=admin_feedback();
        $title='Feedback';
        $view=__DIR__.'/../views/admin/feedback.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }

