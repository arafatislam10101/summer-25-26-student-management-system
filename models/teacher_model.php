<?php
require_once __DIR__.'/model.php';
class Teacher extends Model {
    function byUser(int $uid):?array {
        $s=$this->db->prepare('SELECT t.*,u.name,u.email FROM teachers t JOIN users u ON u.id=t.user_id WHERE t.user_id=?');
        $s->bind_param('i',$uid);
        $s->execute();
        return$s->get_result()->fetch_assoc()?:null;
    }
    function classes(int $tid):array {
        $sql="SELECT c.* FROM classes c WHERE c.teacher_id=? OR EXISTS (SELECT 1 FROM teacher_classes tc WHERE tc.class_id=c.id AND tc.teacher_id=?) ORDER BY c.class_name,c.section";
        $s=$this->db->prepare($sql);
        $s->bind_param('ii',$tid,$tid);
        $s->execute();
        return$s->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    function subjects(int $cid):array {
        $s=$this->db->prepare('SELECT id,subject_name FROM subjects WHERE class_id=? ORDER BY subject_name');
        $s->bind_param('i',$cid);
        $s->execute();
        return$s->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    function addSubject(int $tid,int $cid,string $name):bool {
        $name=trim($name);
        if($name===''||strlen($name)>100||!$this->ownsClass($tid,$cid))return false;
        $s=$this->db->prepare('SELECT id FROM subjects WHERE subject_name=? AND class_id=?');
        $s->bind_param('si',$name,$cid);
        $s->execute();
        if($s->get_result()->fetch_assoc())return false;
        $s=$this->db->prepare('INSERT INTO subjects(subject_name,class_id) VALUES(?,?)');
        $s->bind_param('si',$name,$cid);
        return$s->execute();
    }
    function students(int $cid):array {
        $s=$this->db->prepare('SELECT s.id,s.student_id,u.name FROM students s JOIN users u ON u.id=s.user_id WHERE s.class_id=? ORDER BY u.name');
        $s->bind_param('i',$cid);
        $s->execute();
        return$s->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    function saveBackground(int $tid,string $bg):bool {
        $s=$this->db->prepare('UPDATE teachers SET background=? WHERE id=?');
        $s->bind_param('si',$bg,$tid);
        return$s->execute();
    }
    function ownsClass(int $tid,int $cid):bool {
        $s=$this->db->prepare('SELECT c.id FROM classes c WHERE c.id=? AND (c.teacher_id=? OR EXISTS (SELECT 1 FROM teacher_classes tc WHERE tc.class_id=c.id AND tc.teacher_id=?))');
        $s->bind_param('iii',$cid,$tid,$tid);
        $s->execute();
        return(bool)$s->get_result()->fetch_assoc();
    }
    function saveAttendance(int $tid,int $cid,string $date,array $statuses):bool {
        if(!$this->ownsClass($tid,$cid)||!valid_date($date)||empty($statuses))return false;
        $q=$this->db->prepare('SELECT id FROM students WHERE id=? AND class_id=?');
        $up=$this->db->prepare('INSERT INTO attendance(student_id,class_id,date,status) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id),status=VALUES(status)');
        foreach($statuses as $sid=>$status) {
            $sid=(int)$sid;
            if(!in_array($status,['Present','Absent','Late'],true))continue;
            $q->bind_param('ii',$sid,$cid);
            $q->execute();
            if(!$q->get_result()->fetch_assoc())continue;
            $up->bind_param('iiss',$sid,$cid,$date,$status);
            if(!$up->execute())return false;
        }
        return true;
    }
    function attendanceForDate(int $tid,int $cid,string $date):array {
        if(!$this->ownsClass($tid,$cid)||!valid_date($date))return[];
        $s=$this->db->prepare('SELECT student_id,status FROM attendance WHERE class_id=? AND date=?');
        $s->bind_param('is',$cid,$date);
        $s->execute();
        $out=[];
        foreach($s->get_result()->fetch_all(MYSQLI_ASSOC) as $r)$out[(int)$r['student_id']]=$r['status'];
        return$out;
    }
    function attendanceRange(int $tid,int $cid,string $from,string $to):array {
        if(!$this->ownsClass($tid,$cid))return[];
        $s=$this->db->prepare("SELECT a.date,a.status,s.student_id,u.name FROM attendance a JOIN students s ON s.id=a.student_id JOIN users u ON u.id=s.user_id WHERE a.class_id=? AND a.date BETWEEN ? AND ? ORDER BY a.date DESC,u.name");
        $s->bind_param('iss',$cid,$from,$to);
        $s->execute();
        return$s->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    function saveMark(int $tid,int $sid,int $sub,string $exam,float $marks):bool {
        if($exam===''||$marks<0||$marks>100)return false;
        $s=$this->db->prepare('SELECT st.id FROM students st JOIN subjects su ON su.id=? WHERE st.id=? AND su.class_id=st.class_id');
        $s->bind_param('ii',$sub,$sid);
        $s->execute();
        if(!$s->get_result()->fetch_assoc())return false;
        $s=$this->db->prepare('SELECT class_id FROM students WHERE id=?');
        $s->bind_param('i',$sid);
        $s->execute();
        $row=$s->get_result()->fetch_assoc();
        if(!$row||!$this->ownsClass($tid,(int)$row['class_id']))return false;
        $s=$this->db->prepare('INSERT INTO marks(student_id,subject_id,exam,marks) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE marks=VALUES(marks)');
        $s->bind_param('iisd',$sid,$sub,$exam,$marks);
        return$s->execute();
    }
    function updateMark(int $tid,int $mid,int $sid,int $sub,string $exam,float $marks):bool {
        if($mid<1||$exam===''||$marks<0||$marks>100)return false;
        $s=$this->db->prepare('SELECT m.id,st.class_id FROM marks m JOIN students st ON st.id=m.student_id JOIN subjects su ON su.id=m.subject_id WHERE m.id=? AND st.id=? AND su.id=? AND su.class_id=st.class_id');
        $s->bind_param('iii',$mid,$sid,$sub);
        $s->execute();
        $row=$s->get_result()->fetch_assoc();
        if(!$row||!$this->ownsClass($tid,(int)$row['class_id']))return false;
        $s=$this->db->prepare('UPDATE marks SET student_id=?,subject_id=?,exam=?,marks=? WHERE id=?');
        $s->bind_param('iisdi',$sid,$sub,$exam,$marks,$mid);
        return$s->execute();
    }
    function marks(int $tid,int $cid):array {
        if(!$this->ownsClass($tid,$cid))return[];
        $s=$this->db->prepare('SELECT m.id,st.id AS student_pk,st.student_id,u.name,sub.subject_name,m.exam,m.marks FROM marks m JOIN students st ON st.id=m.student_id JOIN users u ON u.id=st.user_id JOIN subjects sub ON sub.id=m.subject_id WHERE st.class_id=? ORDER BY u.name,sub.subject_name,m.exam');
        $s->bind_param('i',$cid);
        $s->execute();
        return$s->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    function availability(int $tid):array {
        $s=$this->db->prepare('SELECT * FROM teacher_availability WHERE teacher_id=? ORDER BY FIELD(day,"Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"),from_time');
        $s->bind_param('i',$tid);
        $s->execute();
        return$s->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    function saveAvailability(int $tid,string $day,string $from,string $to):bool {
        $days=['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        if(!in_array($day,$days,true)||!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/',$from)||!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/',$to)||$from>=$to)return false;
        $s=$this->db->prepare('SELECT id FROM teacher_availability WHERE teacher_id=? AND day=? AND from_time=?');
        $s->bind_param('iss',$tid,$day,$from);
        $s->execute();
        $row=$s->get_result()->fetch_assoc();
        if($row) {
            $u=$this->db->prepare('UPDATE teacher_availability SET to_time=? WHERE id=? AND teacher_id=?');
            $u->bind_param('sii',$to,$row['id'],$tid);
            return$u->execute();
        }
        $i=$this->db->prepare('INSERT INTO teacher_availability(teacher_id,day,from_time,to_time) VALUES(?,?,?,?)');
        $i->bind_param('isss',$tid,$day,$from,$to);
        return$i->execute();
    }
    function deleteAvailability(int $tid,int $id):bool {
        $s=$this->db->prepare('DELETE FROM teacher_availability WHERE id=? AND teacher_id=?');
        $s->bind_param('ii',$id,$tid);
        return$s->execute();
    }
}
