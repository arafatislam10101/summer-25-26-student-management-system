<?php
require_once __DIR__.'/model.php';

class Student extends Model {

    function byUser(int $uid):?array {
        $s=$this->db->prepare('SELECT s.*,u.name,u.email,u.status,c.class_name,c.section,COALESCE(pu.name,"No parent") parent_name FROM students s JOIN users u ON u.id=s.user_id LEFT JOIN classes c ON c.id=s.class_id LEFT JOIN parents p ON p.id=s.parent_id LEFT JOIN users pu ON pu.id=p.user_id WHERE s.user_id=?');
        $s->bind_param('i',$uid);
        $s->execute();
        return$s->get_result()->fetch_assoc()?:null;
    }

    function attendance(int $sid,string $from='',string $to=''):array {
        $sql='SELECT date,status FROM attendance WHERE student_id=?';
        if($from&&$to)$sql.=' AND date BETWEEN ? AND ?';
        $sql.=' ORDER BY date DESC';
        $s=$this->db->prepare($sql);
        if($from&&$to)$s->bind_param('iss',$sid,$from,$to);
        else$s->bind_param('i',$sid);
        $s->execute();
        return$s->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function attendanceSummary(int $sid):array {
        $s=$this->db->prepare("SELECT COUNT(*) total,SUM(status='Present') present,SUM(status='Late') late,SUM(status='Absent') absent FROM attendance WHERE student_id=?");
        $s->bind_param('i',$sid);
        $s->execute();
        $r=$s->get_result()->fetch_assoc();
        $r['percentage']=$r['total']?round((($r['present']+$r['late'])/$r['total'])*100,2):0;
        return$r;
    }

    function results(int $sid):array {
        $s=$this->db->prepare('SELECT sub.subject_name,m.exam,m.marks FROM marks m JOIN subjects sub ON sub.id=m.subject_id WHERE m.student_id=? ORDER BY sub.subject_name,m.exam');
        $s->bind_param('i',$sid);
        $s->execute();
        return$s->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function notices():array {
        $r=$this->db->query('SELECT * FROM notices ORDER BY id DESC');
        return$r?$r->fetch_all(MYSQLI_ASSOC):[];
    }

    function teacherAvailability():array {
        $r=$this->db->query('SELECT u.name AS teacher_name,t.teacher_id,t.subject,ta.day,ta.from_time,ta.to_time FROM teacher_availability ta JOIN teachers t ON t.id=ta.teacher_id JOIN users u ON u.id=t.user_id WHERE u.status="active" ORDER BY u.name,FIELD(ta.day,"Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"),ta.from_time');
        return$r?$r->fetch_all(MYSQLI_ASSOC):[];
    }

        function leave(int $sid,string $reason,string $from,string $to):bool {
        if(!valid_date($from)||!valid_date($to)||$from>$to)return false;
        $s=$this->db->prepare('INSERT INTO leave_requests(student_id,reason,from_date,to_date) VALUES(?,?,?,?)');
        $s->bind_param('isss',$sid,$reason,$from,$to);
        return$s->execute();
    }

    function online(int $sid,string $type,string $reason):bool {
        if(!in_array($type,['Online','Offline'],true))return false;
        $s=$this->db->prepare('INSERT INTO online_requests(student_id,request_type,reason) VALUES(?,?,?)');
        $s->bind_param('iss',$sid,$type,$reason);
        return$s->execute();
    }

    function requests(int $sid):array {

    $s=$this->db->prepare("
        SELECT 
            id,
            'Leave' type,
            reason,
            status,
            from_date,
            to_date
        FROM leave_requests
        WHERE student_id=?

        UNION ALL

        SELECT 
            id,
            CONCAT('Online/',request_type) type,
            reason,
            status,
            NULL from_date,
            NULL to_date
        FROM online_requests
        WHERE student_id=?

        ORDER BY id DESC
    ");

    $s->bind_param('ii',$sid,$sid);

    $s->execute();

    return $s->get_result()->fetch_all(MYSQLI_ASSOC);
}

    function updateProfile(int $userId,string $name,string $email,string $phone,string $address):bool {
        $this->db->begin_transaction();

        try {
            $s=$this->db->prepare('UPDATE users SET name=?,email=? WHERE id=?');
            $s->bind_param('ssi',$name,$email,$userId);
            $s->execute();

            $s=$this->db->prepare('UPDATE students SET phone=?,address=? WHERE user_id=?');
            $s->bind_param('ssi',$phone,$address,$userId);
            $s->execute();

            $this->db->commit();
            return true;

        } catch(Throwable $e) {
            $this->db->rollback();
            return false;
        }
    }

    function requestById(int $id,string $type):?array {

    if(str_starts_with($type,'Online/')) {

        $s=$this->db->prepare(
            'SELECT id,request_type,reason,status,NULL from_date,NULL to_date 
             FROM online_requests 
             WHERE id=?'
        );

    } else {

        $s=$this->db->prepare(
            'SELECT id,"Leave" type,reason,status,from_date,to_date 
             FROM leave_requests 
             WHERE id=?'
        );

    }


    $s->bind_param('i',$id);

    $s->execute();

    return $s->get_result()->fetch_assoc()?:null;

}



function updateRequest(
    int $id,
    string $type,
    string $reason,
    string $from='',
    string $to=''
):bool {


    if(str_starts_with($type,'Online/')) {


        $s=$this->db->prepare(
            'UPDATE online_requests 
             SET reason=? 
             WHERE id=?'
        );


        $s->bind_param('si',$reason,$id);


    } else {


        if(!valid_date($from)||!valid_date($to)||$from>$to) {
            return false;
        }


        $s=$this->db->prepare(
            'UPDATE leave_requests 
             SET reason=?,from_date=?,to_date=? 
             WHERE id=?'
        );


        $s->bind_param('sssi',$reason,$from,$to,$id);

    }


    return $s->execute();

}



function deleteRequest(int $id,string $type):bool {


    if(str_starts_with($type,'Online/')) {


        $s=$this->db->prepare(
            'DELETE FROM online_requests 
             WHERE id=?'
        );


    } else {


        $s=$this->db->prepare(
            'DELETE FROM leave_requests 
             WHERE id=?'
        );

    }


    $s->bind_param('i',$id);

    return $s->execute();

}

}