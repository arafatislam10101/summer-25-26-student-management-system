<?php
require_once __DIR__.'/../models/user_model.php';
class AuthController {
    function signup():void {
        if(!empty($_SESSION['user']))redirect('dashboard');
        $title='Sign Up';
        $view=__DIR__.'/../views/auth/signup.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }

    function register():void {
        check_csrf();

        $role=trim($_POST['role']??'');
        $name=trim($_POST['name']??'');
        $email=trim($_POST['email']??'');
        $password=$_POST['password']??'';
        $confirm=$_POST['confirm_password']??'';

        if(!in_array($role,['student','teacher','parent'],true)){
            flash('Please select Student, Teacher or Parent.');
            redirect('signup');
        }
        if($name==='' || !valid_email($email)){
            flash('Please enter a valid name and email.');
            redirect('signup');
        }
        if(strlen($password)<8 || $password!==$confirm){
            flash('Password must be at least 8 characters and both passwords must match.');
            redirect('signup');
        }

        $studentId=trim($_POST['student_id']??'');
        $teacherId=trim($_POST['teacher_id']??'');
        $phone=trim($_POST['phone']??'');
        $address=trim($_POST['address']??'');
        $subject=trim($_POST['subject']??'');
        $background=trim($_POST['background']??'');

        if($phone==='' || !valid_contact($phone)){
            flash('Please enter a valid phone number.');
            redirect('signup');
        }
        if($role==='student' && $studentId===''){
            flash('Student ID is required.');
            redirect('signup');
        }
        if($role==='teacher' && $teacherId===''){
            flash('Teacher ID is required.');
            redirect('signup');
        }

        $userModel=new User;
        if($userModel->findByEmail($email)){
            flash('This email is already registered.');
            redirect('signup');
        }

        try{
            $this->db()->begin_transaction();

            $hash=password_hash($password,PASSWORD_DEFAULT);
            $u=$this->db();
            $stmt=mysqli_prepare($u,'INSERT INTO users(name,email,password,role,status) VALUES(?,?,?,?,\'active\')');
            mysqli_stmt_bind_param($stmt,'ssss',$name,$email,$hash,$role);
            if(!mysqli_stmt_execute($stmt)) throw new Exception('Could not create user account.');
            $userId=mysqli_insert_id($u);

            if($role==='student'){
                $stmt=mysqli_prepare($u,'INSERT INTO students(user_id,student_id,phone,address) VALUES(?,?,?,?)');
                mysqli_stmt_bind_param($stmt,'isss',$userId,$studentId,$phone,$address);
                if(!mysqli_stmt_execute($stmt)) throw new Exception('Student ID may already exist.');
            }elseif($role==='teacher'){
                $stmt=mysqli_prepare($u,'INSERT INTO teachers(user_id,teacher_id,phone,subject,background) VALUES(?,?,?,?,?)');
                mysqli_stmt_bind_param($stmt,'issss',$userId,$teacherId,$phone,$subject,$background);
                if(!mysqli_stmt_execute($stmt)) throw new Exception('Teacher ID may already exist.');
            }else{
                $stmt=mysqli_prepare($u,'INSERT INTO parents(user_id,phone) VALUES(?,?)');
                mysqli_stmt_bind_param($stmt,'is',$userId,$phone);
                if(!mysqli_stmt_execute($stmt)) throw new Exception('Could not create parent profile.');
            }

            $this->db()->commit();
            flash('Account created successfully. You can now sign in.');
            redirect('login');
        }catch(Throwable $e){
            $this->db()->rollback();
            flash('Registration failed. Please check your information and try again.');
            redirect('signup');
        }
    }

    private function db(): mysqli {
        return db();
    }

    function login():void {
        if(!empty($_SESSION['user']))redirect('dashboard');
        $title='Login';
        $view=__DIR__.'/../views/auth/login.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
    function authenticate():void {
        check_csrf();
        $email=trim($_POST['email']??'');
        $pass=$_POST['password']??'';
        $u=(new User)->findByEmail($email);
        if(!$u||$u['status']!=='active'||!password_verify($pass,$u['password'])) {
            flash('Invalid email or password.');
            redirect('login');
        }
        session_regenerate_id(true);
        $_SESSION['user']=['id'=>(int)$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']];
        $_SESSION['last_active']=time();
        $_SESSION['last_activity']=$_SESSION['last_active'];
        if(!empty($_POST['remember']))setcookie('remember_email',$email,['expires'=>time()+86400*30,'path'=>'/','httponly'=>true,'samesite'=>'Lax']);
        redirect('dashboard');
    }
    function logout():void {
        $_SESSION=[];
        if(ini_get('session.use_cookies')) {
            $p=session_get_cookie_params();
            setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']);
        }
        session_destroy();
        redirect('login');
    }
}
