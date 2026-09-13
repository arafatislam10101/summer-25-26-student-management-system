<?php
require_once __DIR__.'/../models/admin_model.php';
require_once __DIR__.'/../models/user_model.php';
class AdminController {
    private Admin $m;
    function __construct() {
        $this->m=new Admin;
    }
    function dashboard() {
        role('admin');
        $stats=$this->m->stats();
        $title='Admin Dashboard';
        $view=__DIR__.'/../views/admin/dashboard.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
    function students() {
        role('admin');
        $edit=null;
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $r=null;
            if(isset($_POST['delete']))$r=[$this->m->deleteStudent((int)$_POST['delete']), 'Student deleted.'];
            elseif(isset($_POST['update']))$r=$this->m->updateStudent(['id'=>(int)$_POST['id'],'user_id'=>(int)$_POST['user_id'],'name'=>trim($_POST['name']),'email'=>trim($_POST['email']),'student_id'=>trim($_POST['student_id']),'phone'=>trim($_POST['phone']),'address'=>trim($_POST['address']),'class_id'=>(int)($_POST['class_id']??0),'parent_id'=>(int)($_POST['parent_id']??0)]);
            else$r=$this->m->createUserStudent(['name'=>trim($_POST['name']),'email'=>trim($_POST['email']),'password'=>$_POST['password'],'student_id'=>trim($_POST['student_id']),'phone'=>trim($_POST['phone']),'address'=>trim($_POST['address']),'class_id'=>(int)($_POST['class_id']??0),'parent_id'=>(int)($_POST['parent_id']??0)]);
            flash($r[1]);
            redirect('admin/students');
        }
        $students=$this->m->students();
        $classes=$this->m->classes();
        $parents=$this->m->parents();
        if(isset($_GET['edit']))$edit=$this->m->student((int)$_GET['edit']);
        $title='Manage Students';
        $view=__DIR__.'/../views/admin/students.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
    function teachers() {
        role('admin');
        $edit=null;
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            if(isset($_POST['delete']))$r=[$this->m->deleteTeacher((int)$_POST['delete']),'Teacher deleted.'];
            elseif(isset($_POST['update']))$r=$this->m->updateTeacher(['id'=>(int)$_POST['id'],'user_id'=>(int)$_POST['user_id'],'name'=>trim($_POST['name']),'email'=>trim($_POST['email']),'teacher_id'=>trim($_POST['teacher_id']),'phone'=>trim($_POST['phone']),'subject'=>trim($_POST['subject']),'background'=>trim($_POST['background'])]);
            else$r=$this->m->createUserTeacher(['name'=>trim($_POST['name']),'email'=>trim($_POST['email']),'password'=>$_POST['password'],'teacher_id'=>trim($_POST['teacher_id']),'phone'=>trim($_POST['phone']),'subject'=>trim($_POST['subject']),'background'=>trim($_POST['background'])]);
            flash($r[1]);
            redirect('admin/teachers');
        }
        $teachers=$this->m->teachers();
        $edit=isset($_GET['edit'])?$this->m->teacher((int)$_GET['edit']):null;
        $title='Manage Teachers';
        $view=__DIR__.'/../views/admin/teachers.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
    function teacherBackground() {
        role('admin');
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $teacherId=(int)($_POST['teacher_id']??0);
            $background=trim($_POST['background']??'');
            $ok=$this->m->updateTeacherBackground($teacherId,$background);
            flash($ok?'Teacher background updated.':'Unable to update teacher background.');
            redirect('admin/teacher-background&edit='.$teacherId);
        }
        $teachers=$this->m->teachers();
        $edit=null;
        if(isset($_GET['edit'])) $edit=$this->m->teacher((int)$_GET['edit']);
        $title='Teacher Background';
        $view=__DIR__.'/../views/admin/teacher_background.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
    function classes() {
        role('admin');
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $ok=false;
            if($_POST['entity']==='class') {
                $ok=$this->m->saveClassWithSubject(
                    (int)($_POST['id']??0),
                    trim($_POST['name']??''),
                    trim($_POST['section']??''),
                    (int)($_POST['teacher_id']??0),
                    (int)($_POST['subject_id']??0),
                    trim($_POST['subject_name']??'')
                );
            } else {
                $ok=$this->m->saveSubject((int)($_POST['id']??0),trim($_POST['subject_name']??''),(int)($_POST['class_id']??0));
            }
            flash($ok?'Saved successfully.':'Unable to save. Check duplicates.');
            redirect('admin/classes');
        }
        $classes=$this->m->classes();
        $subjects=$this->m->subjects();
        $teachers=$this->m->teachers();
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
    function deleteClass() {
        role('admin');
        check_csrf();
        $ok=$this->m->deleteClass((int)$_POST['id']);
        json_response($ok,$ok?'Class deleted.':'Unable to delete class.');
    }
    function deleteSubject() {
        role('admin');
        check_csrf();
        $ok=$this->m->deleteSubject((int)$_POST['id']);
        json_response($ok,$ok?'Subject deleted.':'Unable to delete subject.');
    }
    function notices() {
        role('admin');
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $ok=isset($_POST['delete'])?$this->m->deleteNotice((int)$_POST['delete']):$this->m->saveNotice((int)($_POST['id']??0),trim($_POST['title']),trim($_POST['description']));
            flash($ok?'Notice saved.':'Unable to save notice.');
            redirect('admin/notices');
        }
        $notices=$this->m->notices();
        $edit=null;
        if(isset($_GET['edit']))foreach($notices as $n)if((int)$n['id']===(int)$_GET['edit'])$edit=$n;
        $title='Notices';
        $view=__DIR__.'/../views/admin/notices.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
    function users() {
        role('admin');
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            if((int)$_POST['id']!==$_SESSION['user']['id'])$this->m->toggle((int)$_POST['id']);
            redirect('admin/users');
        }
        $users=(new User)->all();
        $title='User Accounts';
        $view=__DIR__.'/../views/admin/users.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
    function requests() {
        role('admin');
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $this->m->setRequest($_POST['type'],(int)$_POST['id'],$_POST['status']);
            redirect('admin/requests');
        }
        $requests=$this->m->requests();
        $title='Request Management';
        $view=__DIR__.'/../views/admin/requests.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
    function atRisk() {
        role('admin');
        $db=db();
        $q=$db->query("SELECT s.student_id,u.name,(SELECT ROUND(COALESCE(100*SUM(a.status IN ('Present','Late'))/NULLIF(COUNT(*),0),0),2) FROM attendance a WHERE a.student_id=s.id) attendance_pct,(SELECT ROUND(COALESCE(AVG(m.marks),0),2) FROM marks m WHERE m.student_id=s.id) average_marks FROM students s JOIN users u ON u.id=s.user_id HAVING attendance_pct<60 OR average_marks<50 ORDER BY attendance_pct,average_marks");
        $atRisk=$q?$q->fetch_all(MYSQLI_ASSOC):[];
        $title='At-Risk Students';
        $view=__DIR__.'/../views/admin/at_risk.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
    function feedback() {
        role('admin');
        $feedback=$this->m->feedback();
        $title='Feedback';
        $view=__DIR__.'/../views/admin/feedback.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
}
