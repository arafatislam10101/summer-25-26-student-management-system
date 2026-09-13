<?php
require_once __DIR__.'/../models/model.php';
class ParentController {
    private mysqli $db;
    function __construct() {
        $this->db=db();
    }
    private function child():array {
        $u=role('parent');
        $s=$this->db->prepare('SELECT s.*,su.name student_name,su.email student_email,c.class_name,c.section FROM parents p JOIN students s ON s.parent_id=p.id JOIN users su ON su.id=s.user_id LEFT JOIN classes c ON c.id=s.class_id WHERE p.user_id=? ORDER BY s.id LIMIT 1');
        $s->bind_param('i',$u['id']);
        $s->execute();
        $r=$s->get_result()->fetch_assoc();
        if(!$r) {
            http_response_code(404);
            exit('No child linked to this parent.');
        }
        return$r;
    }
    function dashboard() {
        $child=$this->child();
        $title='Parent Dashboard';
        $view=__DIR__.'/../views/parent/dashboard.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
    function attendance() {
        $child=$this->child();
        $s=$this->db->prepare("SELECT COUNT(*) total,SUM(status='Present') present,SUM(status='Late') late,SUM(status='Absent') absent FROM attendance WHERE student_id=?");
        $s->bind_param('i',$child['id']);
        $s->execute();
        $summary=$s->get_result()->fetch_assoc();
        $summary['percentage']=$summary['total']?round((($summary['present']+$summary['late'])/$summary['total'])*100,2):0;
        $s=$this->db->prepare('SELECT date,status FROM attendance WHERE student_id=? ORDER BY date DESC');
        $s->bind_param('i',$child['id']);
        $s->execute();
        $records=$s->get_result()->fetch_all(MYSQLI_ASSOC);
        $title="Child's Attendance";
        $view=__DIR__.'/../views/parent/attendance.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
    function results() {
        $child=$this->child();
        $s=$this->db->prepare('SELECT sub.subject_name,m.exam,m.marks FROM marks m JOIN subjects sub ON sub.id=m.subject_id WHERE m.student_id=? ORDER BY sub.subject_name');
        $s->bind_param('i',$child['id']);
        $s->execute();
        $records=$s->get_result()->fetch_all(MYSQLI_ASSOC);
        $title="Child's Results";
        $view=__DIR__.'/../views/parent/results.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
    function atRisk() {
        $child=$this->child();
        // Always initialize these values so the view never receives null arrays.
        $a=['attendance_pct'=>0.00];
        $m=['average_marks'=>0.00];
        $s=$this->db->prepare("SELECT ROUND(COALESCE(100 * SUM(status IN ('Present','Late')) / NULLIF(COUNT(*),0),0),2) AS attendance_pct FROM attendance WHERE student_id=?");
        if($s) {
            $s->bind_param('i',$child['id']);
            if($s->execute()) {
                $row=$s->get_result()->fetch_assoc();
                if(is_array($row)) $a['attendance_pct']=(float)($row['attendance_pct'] ?? 0);
            }
            $s->close();
        }
        $s=$this->db->prepare('SELECT ROUND(COALESCE(AVG(marks),0),2) AS average_marks FROM marks WHERE student_id=?');
        if($s) {
            $s->bind_param('i',$child['id']);
            if($s->execute()) {
                $row=$s->get_result()->fetch_assoc();
                if(is_array($row)) $m['average_marks']=(float)($row['average_marks'] ?? 0);
            }
            $s->close();
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
    function notices() {
        $u=role('parent');
        $r=$this->db->query('SELECT * FROM notices ORDER BY id DESC');
        $notices=$r?$r->fetch_all(MYSQLI_ASSOC):[];
        $child=$this->child();
        $title='Notices & Accounts';
        $view=__DIR__.'/../views/parent/notices.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
    function payment() {
        $child=$this->child();
        $s=$this->db->prepare('SELECT id,amount,purpose,status,paid_at,payment_method,transaction_id FROM payments WHERE student_id=? ORDER BY id DESC');
        $s->bind_param('i',$child['id']);
        $s->execute();
        $payments=$s->get_result()->fetch_all(MYSQLI_ASSOC);
        $s->close();
        $title='Payment System / Accounts';
        $view=__DIR__.'/../views/parent/payment.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
    function makePayment() {
        role('parent');
        $child=$this->child();
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
        $s=$this->db->prepare("INSERT INTO payments(student_id,amount,purpose,status,paid_at,payment_method,transaction_id) VALUES(?,?,?,?,CURDATE(),?,?)");
        if(!$s) {
            flash('Unable to process payment. Please run the database update first.');
            redirect('parent/payment');
        }
        $status='Paid';
        $s->bind_param('idssss',$child['id'],$amount,$purpose,$status,$method,$transaction);
        $ok=$s->execute();
        $s->close();
        flash($ok?'Payment completed successfully. Your payment slip is ready to print.':'Payment could not be completed.');
        redirect('parent/payment');
    }
    function printPayment() {
        role('parent');
        $c=$this->child();
        $paymentId=(int)($_GET['id'] ?? 0);
        if($paymentId<=0) {
            http_response_code(400);
            exit('Invalid payment slip.');
        }
        $s=$this->db->prepare('SELECT p.id,p.amount,p.purpose,p.status,p.paid_at,s.student_id,su.name AS student_name,c.class_name,c.section
                         FROM payments p
                         JOIN students s ON s.id=p.student_id
                         JOIN users su ON su.id=s.user_id
                         LEFT JOIN classes c ON c.id=s.class_id
                         WHERE p.id=? AND p.student_id=? LIMIT 1');
        if(!$s) {
            http_response_code(500);
            exit('Unable to load payment slip.');
        }
        $s->bind_param('ii',$paymentId,$c['id']);
        $s->execute();
        $payment=$s->get_result()->fetch_assoc();
        $s->close();
        if(!$payment) {
            http_response_code(404);
            exit('Payment slip not found.');
        }
        $title='Payment Slip';
        $view=__DIR__.'/../views/parent/payment_slip.php';
        include $view;
    }
    function requests() {
        role('parent');
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $c=$this->child();
            $name=trim($_POST['package_name']);
            $reason=trim($_POST['reason']);
            $s=$this->db->prepare('INSERT INTO package_requests(student_id,package_name,reason) VALUES(?,?,?)');
            $s->bind_param('iss',$c['id'],$name,$reason);
            $s->execute();
            flash('Package request submitted.');
            redirect('parent/requests');
        }
        $c=$this->child();
        $s=$this->db->prepare('SELECT package_name,reason,status,created_at FROM package_requests WHERE student_id=? ORDER BY id DESC');
        $s->bind_param('i',$c['id']);
        $s->execute();
        $requests=$s->get_result()->fetch_all(MYSQLI_ASSOC);
        $title='Package Request';
        $view=__DIR__.'/../views/parent/package.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
    function rating() {
        role('parent');
        if($_SERVER['REQUEST_METHOD']==='POST') {
            check_csrf();
            $c=$this->child();
            $rating=max(1,min(5,(int)$_POST['rating']));
            $comment=trim($_POST['comment']);
            $s=$this->db->prepare('INSERT INTO ratings(student_id,rating,comment) VALUES(?,?,?)');
            $s->bind_param('iis',$c['id'],$rating,$comment);
            $s->execute();
            flash('Feedback/rating submitted.');
            redirect('parent/rating');
        }
        $c=$this->child();
        $s=$this->db->prepare('SELECT rating,comment,created_at FROM ratings WHERE student_id=? ORDER BY id DESC');
        $s->bind_param('i',$c['id']);
        $s->execute();
        $feedback=$s->get_result()->fetch_all(MYSQLI_ASSOC);
        $title='Rating & Feedback';
        $view=__DIR__.'/../views/parent/rating.php';
        include __DIR__.'/../views/partials/header.php';
        include $view;
        include __DIR__.'/../views/partials/footer.php';
    }
}
