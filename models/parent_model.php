<?php
require_once __DIR__.'/model.php';

class ParentModel extends Model {

    /**
     * Get the parent profile by the logged-in user's ID.
     */
    function byUser(int $uid):?array {
        $s=$this->db->prepare('SELECT p.*,u.name,u.email,u.status FROM parents p JOIN users u ON u.id=p.user_id WHERE p.user_id=? LIMIT 1');
        $s->bind_param('i',$uid);
        $s->execute();
        return $s->get_result()->fetch_assoc()?:null;
    }

    /**
     * Get the child linked to this parent.
     */
    function child(int $uid):?array {
        $s=$this->db->prepare('SELECT s.*,su.name AS student_name,su.email AS student_email,su.status,c.class_name,c.section FROM parents p JOIN students s ON s.parent_id=p.id JOIN users su ON su.id=s.user_id LEFT JOIN classes c ON c.id=s.class_id WHERE p.user_id=? ORDER BY s.id LIMIT 1');
        $s->bind_param('i',$uid);
        $s->execute();
        return $s->get_result()->fetch_assoc()?:null;
    }

    /**
     * Get all children linked to this parent.
     */
    function children(int $uid):array {
        $s=$this->db->prepare('SELECT s.*,su.name AS student_name,su.email AS student_email,su.status,c.class_name,c.section FROM parents p JOIN students s ON s.parent_id=p.id JOIN users su ON su.id=s.user_id LEFT JOIN classes c ON c.id=s.class_id WHERE p.user_id=? ORDER BY s.id');
        $s->bind_param('i',$uid);
        $s->execute();
        return $s->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function attendance(int $studentId):array {
        $s=$this->db->prepare('SELECT date,status FROM attendance WHERE student_id=? ORDER BY date DESC');
        $s->bind_param('i',$studentId);
        $s->execute();
        return $s->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function attendanceSummary(int $studentId):array {
        $s=$this->db->prepare("SELECT COUNT(*) total,SUM(status='Present') present,SUM(status='Late') late,SUM(status='Absent') absent FROM attendance WHERE student_id=?");
        $s->bind_param('i',$studentId);
        $s->execute();
        $r=$s->get_result()->fetch_assoc()?:['total'=>0,'present'=>0,'late'=>0,'absent'=>0];
        $r['percentage']=$r['total']?round((($r['present']+$r['late'])/$r['total'])*100,2):0;
        return $r;
    }

    function results(int $studentId):array {
        $s=$this->db->prepare('SELECT sub.subject_name,m.exam,m.marks FROM marks m JOIN subjects sub ON sub.id=m.subject_id WHERE m.student_id=? ORDER BY sub.subject_name,m.exam');
        $s->bind_param('i',$studentId);
        $s->execute();
        return $s->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function notices():array {
        $r=$this->db->query('SELECT * FROM notices ORDER BY id DESC');
        return $r?$r->fetch_all(MYSQLI_ASSOC):[];
    }

    function payments(int $studentId):array {
        $s=$this->db->prepare('SELECT id,amount,purpose,status,paid_at,payment_method,transaction_id FROM payments WHERE student_id=? ORDER BY id DESC');
        $s->bind_param('i',$studentId);
        $s->execute();
        return $s->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function updateProfile(int $userId,string $name,string $email,string $phone):bool {
        $this->db->begin_transaction();
        try {
            $s=$this->db->prepare('UPDATE users SET name=?,email=? WHERE id=? AND role=\'parent\'');
            $s->bind_param('ssi',$name,$email,$userId);
            $s->execute();

            $s=$this->db->prepare('UPDATE parents SET phone=? WHERE user_id=?');
            $s->bind_param('si',$phone,$userId);
            $s->execute();

            $this->db->commit();
            return true;
        } catch(Throwable $e) {
            $this->db->rollback();
            return false;
        }
    }
}
