<?php
require_once __DIR__.'/../models/model.php';
function parent_controller_child():array {
        $u=role('parent');
        $s=mysqli_prepare(db(), 'SELECT s.*,su.name student_name,su.email student_email,c.class_name,c.section FROM parents p JOIN students s ON s.parent_id=p.id JOIN users su ON su.id=s.user_id LEFT JOIN classes c ON c.id=s.class_id WHERE p.user_id=? ORDER BY s.id LIMIT 1');
        mysqli_stmt_bind_param($s, 'i',$u['id']);
        mysqli_stmt_execute($s);
        $r=mysqli_fetch_assoc(mysqli_stmt_get_result($s));
        if(!$r) {
            http_response_code(404);
            exit('No child linked to this parent.');
        }
        return$r;
    }
function parent_controller_dashboard() {
        $child=parent_controller_child();
        $title='Parent Dashboard';
        $view=__DIR__.'/../views/parent/dashboard.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function parent_controller_attendance() {
        $child=parent_controller_child();
        $s=mysqli_prepare(db(), "SELECT COUNT(*) total,SUM(status='Present') present,SUM(status='Late') late,SUM(status='Absent') absent FROM attendance WHERE student_id=?");
        mysqli_stmt_bind_param($s, 'i',$child['id']);
        mysqli_stmt_execute($s);
        $summary=mysqli_fetch_assoc(mysqli_stmt_get_result($s));
        $summary['percentage']=$summary['total']?round((($summary['present']+$summary['late'])/$summary['total'])*100,2):0;
        $s=mysqli_prepare(db(), 'SELECT date,status FROM attendance WHERE student_id=? ORDER BY date DESC');
        mysqli_stmt_bind_param($s, 'i',$child['id']);
        mysqli_stmt_execute($s);
        $records=mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
        $title="Child's Attendance";
        $view=__DIR__.'/../views/parent/attendance.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function parent_controller_results() {
        $child=parent_controller_child();
        $s=mysqli_prepare(db(), 'SELECT sub.subject_name,m.exam,m.marks FROM marks m JOIN subjects sub ON sub.id=m.subject_id WHERE m.student_id=? ORDER BY sub.subject_name');
        mysqli_stmt_bind_param($s, 'i',$child['id']);
        mysqli_stmt_execute($s);
        $records=mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
        $title="Child's Results";
        $view=__DIR__.'/../views/parent/results.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function parent_controller_atRisk() {
        $child=parent_controller_child();
        // Always initialize these values so the view never receives null arrays.
        $a=['attendance_pct'=>0.00];
        $m=['average_marks'=>0.00];
        $s=mysqli_prepare(db(), "SELECT ROUND(COALESCE(100 * SUM(status IN ('Present','Late')) / NULLIF(COUNT(*),0),0),2) AS attendance_pct FROM attendance WHERE student_id=?");
        if($s) {
            mysqli_stmt_bind_param($s, 'i',$child['id']);
            if(mysqli_stmt_execute($s)) {
                $row=mysqli_fetch_assoc(mysqli_stmt_get_result($s));
                if(is_array($row)) $a['attendance_pct']=(float)($row['attendance_pct'] ?? 0);
            }
            mysqli_stmt_close($s);
        }
        $s=mysqli_prepare(db(), 'SELECT ROUND(COALESCE(AVG(marks),0),2) AS average_marks FROM marks WHERE student_id=?');
        if($s) {
            mysqli_stmt_bind_param($s, 'i',$child['id']);
            if(mysqli_stmt_execute($s)) {
                $row=mysqli_fetch_assoc(mysqli_stmt_get_result($s));
                if(is_array($row)) $m['average_marks']=(float)($row['average_marks'] ?? 0);
            }
            mysqli_stmt_close($s);
        }
        $attendance=(float)$a['attendance_pct'];
        $average=(float)$m['average_marks'];
        $isRisk=($attendance < 60 || $average < 50);
        $title='At-Risk Alerts';
        $view=__DIR__.'/../views/parent/at_risk.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function parent_controller_notices() {
        $u=role('parent');
                $notices=db_fetch_all('SELECT * FROM notices ORDER BY id DESC');
        $child=parent_controller_child();
        $title='Notices & Accounts';
        $view=__DIR__.'/../views/parent/notices.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function parent_controller_payment() {
        $child=parent_controller_child();
        $s=mysqli_prepare(db(), 'SELECT id,amount,purpose,status,paid_at,payment_method,transaction_id FROM payments WHERE student_id=? ORDER BY id DESC');
        mysqli_stmt_bind_param($s, 'i',$child['id']);
        mysqli_stmt_execute($s);
        $payments=mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
        mysqli_stmt_close($s);
        $title='Payment System / Accounts';
        $view=__DIR__.'/../views/parent/payment.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function parent_controller_makePayment() {
        role('parent');
        $child=parent_controller_child();
        if($_SERVER['REQUEST_METHOD']!=='POST') {
            redirect('parent/payment');
        }
        check_csrf();
        $amount=(float)($_POST['amount']??0);
        $purpose=trim($_POST['purpose']??'');
        $method=trim($_POST['payment_method']??'');
        $transaction=trim($_POST['transaction_id']??'');
        $allowed=['bKash','Nagad','Card','Bank Transfer'];
        if($amount<=0 || $amount>1000000 || $purpose==='' || !in_array($method,$allowed,true)) {
            flash('Please enter valid payment information.');
            redirect('parent/payment');
        }
        if(in_array($method,['bKash','Nagad','Bank Transfer'],true) && $transaction==='') {
            flash('Transaction ID is required for this payment method.');
            redirect('parent/payment');
        }
        if($method==='Card' && $transaction==='') $transaction='CARD-'.date('YmdHis').'-'.$child['id'];
        $s=mysqli_prepare(db(), "INSERT INTO payments(student_id,amount,purpose,status,paid_at,payment_method,transaction_id) VALUES(?,?,?,?,CURDATE(),?,?)");
        if(!$s) {
            flash('Unable to process payment. Please run the database update first.');
            redirect('parent/payment');
        }
        $status='Paid';
        mysqli_stmt_bind_param($s, 'idssss',$child['id'],$amount,$purpose,$status,$method,$transaction);
        $ok=mysqli_stmt_execute($s);
        mysqli_stmt_close($s);
        flash($ok?'Payment completed successfully. Your payment slip is ready to print.':'Payment could not be completed.');
        redirect('parent/payment');
    }
function parent_controller_printPayment() {
        role('parent');
        $c=parent_controller_child();
        $paymentId=(int)($_GET['id'] ?? 0);
        if($paymentId<=0) {
            http_response_code(400);
            exit('Invalid payment slip.');
        }
        $s=mysqli_prepare(db(), 'SELECT p.id,p.amount,p.purpose,p.status,p.paid_at,s.student_id,su.name AS student_name,c.class_name,c.section
                         FROM payments p
                         JOIN students s ON s.id=p.student_id
                         JOIN users su ON su.id=s.user_id
                         LEFT JOIN classes c ON c.id=s.class_id
                         WHERE p.id=? AND p.student_id=? LIMIT 1');
        if(!$s) {
            http_response_code(500);
            exit('Unable to load payment slip.');
        }
        mysqli_stmt_bind_param($s, 'ii',$paymentId,$c['id']);
        mysqli_stmt_execute($s);
        $payment=mysqli_fetch_assoc(mysqli_stmt_get_result($s));
        mysqli_stmt_close($s);
        if(!$payment) {
            http_response_code(404);
            exit('Payment slip not found.');
        }
        $title='Payment Slip';
        $view=__DIR__.'/../views/parent/payment_slip.php';
        include $view;
    }
function parent_controller_requests() {
        role('parent');
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $c=parent_controller_child();
            $name=trim($_POST['package_name']);
            $reason=trim($_POST['reason']);
            $s=mysqli_prepare(db(), 'INSERT INTO package_requests(student_id,package_name,reason) VALUES(?,?,?)');
            mysqli_stmt_bind_param($s, 'iss',$c['id'],$name,$reason);
            mysqli_stmt_execute($s);
            flash('Package request submitted.');
            redirect('parent/requests');
        }
        $c=parent_controller_child();
        $s=mysqli_prepare(db(), 'SELECT package_name,reason,status,created_at FROM package_requests WHERE student_id=? ORDER BY id DESC');
        mysqli_stmt_bind_param($s, 'i',$c['id']);
        mysqli_stmt_execute($s);
        $requests=mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
        $title='Package Request';
        $view=__DIR__.'/../views/parent/package.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
function parent_controller_rating() {
        role('parent');
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $c=parent_controller_child();
            $rating=max(1,min(5,(int)$_POST['rating']));
            $comment=trim($_POST['comment']);
            $s=mysqli_prepare(db(), 'INSERT INTO ratings(student_id,rating,comment) VALUES(?,?,?)');
            mysqli_stmt_bind_param($s, 'iis',$c['id'],$rating,$comment);
            mysqli_stmt_execute($s);
            flash('Feedback/rating submitted.');
            redirect('parent/rating');
        }
        $c=parent_controller_child();
        $s=mysqli_prepare(db(), 'SELECT rating,comment,created_at FROM ratings WHERE student_id=? ORDER BY id DESC');
        mysqli_stmt_bind_param($s, 'i',$c['id']);
        mysqli_stmt_execute($s);
        $feedback=mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
        $title='Rating & Feedback';
        $view=__DIR__.'/../views/parent/rating.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }

