<?php
function dashboard_controller_index() {
        $u=auth();
        switch($u['role']) {
            case'admin':redirect('admin/dashboard');
            case'teacher':redirect('teacher/dashboard');
            case'student':redirect('student/dashboard');
            case'parent':redirect('parent/dashboard');
            default:redirect('login');
        }
    }

