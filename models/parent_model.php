<?php
require_once __DIR__.'/model.php';



    /**
     * Get the parent profile by the logged-in user's ID.
     */
function parent_byUser(int $uid):?array {
        $s=mysqli_prepare(db(), 'SELECT p.*,u.name,u.email,u.status FROM parents p JOIN users u ON u.id=p.user_id WHERE p.user_id=? LIMIT 1');
        mysqli_stmt_bind_param($s, 'i',$uid);
        mysqli_stmt_execute($s);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($s))?:null;
    }

    /**
     * Get the child linked to this parent.
     */
function parent_child(int $uid):?array {
        $s=mysqli_prepare(db(), 'SELECT s.*,su.name AS student_name,su.email AS student_email,su.status,c.class_name,c.section FROM parents p JOIN students s ON s.parent_id=p.id JOIN users su ON su.id=s.user_id LEFT JOIN classes c ON c.id=s.class_id WHERE p.user_id=? ORDER BY s.id LIMIT 1');
        mysqli_stmt_bind_param($s, 'i',$uid);
        mysqli_stmt_execute($s);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($s))?:null;
    }

    /**
     * Get all children linked to this parent.
     */
function parent_children(int $uid):array {
        $s=mysqli_prepare(db(), 'SELECT s.*,su.name AS student_name,su.email AS student_email,su.status,c.class_name,c.section FROM parents p JOIN students s ON s.parent_id=p.id JOIN users su ON su.id=s.user_id LEFT JOIN classes c ON c.id=s.class_id WHERE p.user_id=? ORDER BY s.id');
        mysqli_stmt_bind_param($s, 'i',$uid);
        mysqli_stmt_execute($s);
        return mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
    }
function parent_attendance(int $studentId):array {
        $s=mysqli_prepare(db(), 'SELECT date,status FROM attendance WHERE student_id=? ORDER BY date DESC');
        mysqli_stmt_bind_param($s, 'i',$studentId);
        mysqli_stmt_execute($s);
        return mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
    }
function parent_attendanceSummary(int $studentId):array {
        $s=mysqli_prepare(db(), "SELECT COUNT(*) total,SUM(status='Present') present,SUM(status='Late') late,SUM(status='Absent') absent FROM attendance WHERE student_id=?");
        mysqli_stmt_bind_param($s, 'i',$studentId);
        mysqli_stmt_execute($s);
        $r=mysqli_fetch_assoc(mysqli_stmt_get_result($s))?:['total'=>0,'present'=>0,'late'=>0,'absent'=>0];
        $r['percentage']=$r['total']?round((($r['present']+$r['late'])/$r['total'])*100,2):0;
        return $r;
    }
function parent_results(int $studentId):array {
        $s=mysqli_prepare(db(), 'SELECT sub.subject_name,m.exam,m.marks FROM marks m JOIN subjects sub ON sub.id=m.subject_id WHERE m.student_id=? ORDER BY sub.subject_name,m.exam');
        mysqli_stmt_bind_param($s, 'i',$studentId);
        mysqli_stmt_execute($s);
        return mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
    }
function parent_notices():array {
        return db_fetch_all('SELECT * FROM notices ORDER BY id DESC');
    }
function parent_payments(int $studentId):array {
        $s=mysqli_prepare(db(), 'SELECT id,amount,purpose,status,paid_at,payment_method,transaction_id FROM payments WHERE student_id=? ORDER BY id DESC');
        mysqli_stmt_bind_param($s, 'i',$studentId);
        mysqli_stmt_execute($s);
        return mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
    }
function parent_updateProfile(int $userId,string $name,string $email,string $phone):bool {
        mysqli_begin_transaction(db());
        try {
            $s=mysqli_prepare(db(), 'UPDATE users SET name=?,email=? WHERE id=? AND role=\'parent\'');
            mysqli_stmt_bind_param($s, 'ssi',$name,$email,$userId);
            mysqli_stmt_execute($s);

            $s=mysqli_prepare(db(), 'UPDATE parents SET phone=? WHERE user_id=?');
            mysqli_stmt_bind_param($s, 'si',$phone,$userId);
            mysqli_stmt_execute($s);

            mysqli_commit(db());
            return true;
        } catch(Throwable $e) {
            mysqli_rollback(db());
            return false;
        }
    }

