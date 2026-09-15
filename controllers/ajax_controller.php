<?php
require_once __DIR__.'/../models/admin_model.php';
function ajax_controller_handle() {
        role('admin');
        check_csrf();
        
        $a=$_POST['action']??'';
        try {
            switch($a) {
                case'delete_student':$ok=admin_deleteStudent((int)$_POST['id']);
                break;
                case'delete_teacher':$ok=admin_deleteTeacher((int)$_POST['id']);
                break;
                case'delete_notice':$ok=admin_deleteNotice((int)$_POST['id']);
                break;
                case'delete_class':$ok=admin_deleteClass((int)$_POST['id']);
                break;
                case'delete_subject':$ok=admin_deleteSubject((int)$_POST['id']);
                break;
                case'toggle_user':if((int)$_POST['id']===$_SESSION['user']['id'])json_response(false,'You cannot deactivate your own account.');
                $ok=admin_toggle((int)$_POST['id']);
                break;
                default:json_response(false,'Unknown action.');
            }
            json_response((bool)$ok,$ok?'Action completed.':'Action failed.');
} catch (Throwable $e) {
            json_response(false,'Operation failed.');
        }
    }

