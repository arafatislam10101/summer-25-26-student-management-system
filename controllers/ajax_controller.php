<?php
require_once __DIR__.'/../models/admin_model.php';
class AjaxController {
    function handle() {
        role('admin');
        check_csrf();
        $m=new Admin;
        $a=$_POST['action']??'';
        try {
            switch($a) {
                case'delete_student':$ok=$m->deleteStudent((int)$_POST['id']);
                break;
                case'delete_teacher':$ok=$m->deleteTeacher((int)$_POST['id']);
                break;
                case'delete_notice':$ok=$m->deleteNotice((int)$_POST['id']);
                break;
                case'delete_class':$ok=$m->deleteClass((int)$_POST['id']);
                break;
                case'delete_subject':$ok=$m->deleteSubject((int)$_POST['id']);
                break;
                case'toggle_user':if((int)$_POST['id']===$_SESSION['user']['id'])json_response(false,'You cannot deactivate your own account.');
                $ok=$m->toggle((int)$_POST['id']);
                break;
                default:json_response(false,'Unknown action.');
            }
            json_response((bool)$ok,$ok?'Action completed.':'Action failed.');
} catch (Throwable $e) {
            json_response(false,'Operation failed.');
        }
    }
}
