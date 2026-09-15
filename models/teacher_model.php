<?php
require_once __DIR__.'/model.php';
function teacher_byUser(int $uid):?array {
        $s=mysqli_prepare(db(), 'SELECT t.*,u.name,u.email FROM teachers t JOIN users u ON u.id=t.user_id WHERE t.user_id=?');
        mysqli_stmt_bind_param($s, 'i',$uid);
        mysqli_stmt_execute($s);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($s))?:null;
    }
function teacher_classes(int $tid):array {
        $sql="SELECT c.* FROM classes c WHERE c.teacher_id=? OR EXISTS (SELECT 1 FROM teacher_classes tc WHERE tc.class_id=c.id AND tc.teacher_id=?) ORDER BY c.class_name,c.section";
        $s=mysqli_prepare(db(), $sql);
        mysqli_stmt_bind_param($s, 'ii',$tid,$tid);
        mysqli_stmt_execute($s);
        return mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
    }
function teacher_subjects(int $cid):array {
        $s=mysqli_prepare(db(), 'SELECT id,subject_name FROM subjects WHERE class_id=? ORDER BY subject_name');
        mysqli_stmt_bind_param($s, 'i',$cid);
        mysqli_stmt_execute($s);
        return mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
    }
function teacher_addSubject(int $tid,int $cid,string $name):bool {
        $name=trim($name);
        if($name===''||strlen($name)>100||!teacher_ownsClass($tid,$cid))return false;
        $s=mysqli_prepare(db(), 'SELECT id FROM subjects WHERE subject_name=? AND class_id=?');
        mysqli_stmt_bind_param($s, 'si',$name,$cid);
        mysqli_stmt_execute($s);
        if(mysqli_fetch_assoc(mysqli_stmt_get_result($s)))return false;
        $s=mysqli_prepare(db(), 'INSERT INTO subjects(subject_name,class_id) VALUES(?,?)');
        mysqli_stmt_bind_param($s, 'si',$name,$cid);
        return mysqli_stmt_execute($s);
    }
function teacher_students(int $cid):array {
        $s=mysqli_prepare(db(), 'SELECT s.id,s.student_id,u.name FROM students s JOIN users u ON u.id=s.user_id WHERE s.class_id=? ORDER BY u.name');
        mysqli_stmt_bind_param($s, 'i',$cid);
        mysqli_stmt_execute($s);
        return mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
    }
function teacher_saveBackground(int $tid,string $bg):bool {
        $s=mysqli_prepare(db(), 'UPDATE teachers SET background=? WHERE id=?');
        mysqli_stmt_bind_param($s, 'si',$bg,$tid);
        return mysqli_stmt_execute($s);
    }
function teacher_ownsClass(int $tid,int $cid):bool {
        $s=mysqli_prepare(db(), 'SELECT c.id FROM classes c WHERE c.id=? AND (c.teacher_id=? OR EXISTS (SELECT 1 FROM teacher_classes tc WHERE tc.class_id=c.id AND tc.teacher_id=?))');
        mysqli_stmt_bind_param($s, 'iii',$cid,$tid,$tid);
        mysqli_stmt_execute($s);
        return(bool)mysqli_fetch_assoc(mysqli_stmt_get_result($s));
    }
function teacher_saveAttendance(int $tid,int $cid,string $date,array $statuses):bool {
        if(!teacher_ownsClass($tid,$cid)||!valid_date($date)||empty($statuses))return false;
        $q=mysqli_prepare(db(), 'SELECT id FROM students WHERE id=? AND class_id=?');
        $up=mysqli_prepare(db(), 'INSERT INTO attendance(student_id,class_id,date,status) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE class_id=VALUES(class_id),status=VALUES(status)');
        foreach($statuses as $sid=>$status) {
            $sid=(int)$sid;
            if(!in_array($status,['Present','Absent','Late'],true))continue;
            mysqli_stmt_bind_param($q, 'ii',$sid,$cid);
            mysqli_stmt_execute($q);
            if(!mysqli_fetch_assoc(mysqli_stmt_get_result($q)))continue;
            mysqli_stmt_bind_param($up, 'iiss',$sid,$cid,$date,$status);
            if(!mysqli_stmt_execute($up))return false;
        }
        return true;
    }
function teacher_attendanceForDate(int $tid,int $cid,string $date):array {
        if(!teacher_ownsClass($tid,$cid)||!valid_date($date))return[];
        $s=mysqli_prepare(db(), 'SELECT student_id,status FROM attendance WHERE class_id=? AND date=?');
        mysqli_stmt_bind_param($s, 'is',$cid,$date);
        mysqli_stmt_execute($s);
        $out=[];
        foreach(mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC) as $r)$out[(int)$r['student_id']]=$r['status'];
        return$out;
    }
function teacher_attendanceRange(int $tid,int $cid,string $from,string $to):array {
        if(!teacher_ownsClass($tid,$cid))return[];
        $s=mysqli_prepare(db(), "SELECT a.date,a.status,s.student_id,u.name FROM attendance a JOIN students s ON s.id=a.student_id JOIN users u ON u.id=s.user_id WHERE a.class_id=? AND a.date BETWEEN ? AND ? ORDER BY a.date DESC,u.name");
        mysqli_stmt_bind_param($s, 'iss',$cid,$from,$to);
        mysqli_stmt_execute($s);
        return mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
    }
function teacher_saveMark(int $tid,int $sid,int $sub,string $exam,float $marks):bool {
        if($exam===''||$marks<0||$marks>100)return false;
        $s=mysqli_prepare(db(), 'SELECT st.id FROM students st JOIN subjects su ON su.id=? WHERE st.id=? AND su.class_id=st.class_id');
        mysqli_stmt_bind_param($s, 'ii',$sub,$sid);
        mysqli_stmt_execute($s);
        if(!mysqli_fetch_assoc(mysqli_stmt_get_result($s)))return false;
        $s=mysqli_prepare(db(), 'SELECT class_id FROM students WHERE id=?');
        mysqli_stmt_bind_param($s, 'i',$sid);
        mysqli_stmt_execute($s);
        $row=mysqli_fetch_assoc(mysqli_stmt_get_result($s));
        if(!$row||!teacher_ownsClass($tid,(int)$row['class_id']))return false;
        $s=mysqli_prepare(db(), 'INSERT INTO marks(student_id,subject_id,exam,marks) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE marks=VALUES(marks)');
        mysqli_stmt_bind_param($s, 'iisd',$sid,$sub,$exam,$marks);
        return mysqli_stmt_execute($s);
    }
function teacher_updateMark(int $tid,int $mid,int $sid,int $sub,string $exam,float $marks):bool {
        if($mid<1||$exam===''||$marks<0||$marks>100)return false;
        $s=mysqli_prepare(db(), 'SELECT m.id,st.class_id FROM marks m JOIN students st ON st.id=m.student_id JOIN subjects su ON su.id=m.subject_id WHERE m.id=? AND st.id=? AND su.id=? AND su.class_id=st.class_id');
        mysqli_stmt_bind_param($s, 'iii',$mid,$sid,$sub);
        mysqli_stmt_execute($s);
        $row=mysqli_fetch_assoc(mysqli_stmt_get_result($s));
        if(!$row||!teacher_ownsClass($tid,(int)$row['class_id']))return false;
        $s=mysqli_prepare(db(), 'UPDATE marks SET student_id=?,subject_id=?,exam=?,marks=? WHERE id=?');
        mysqli_stmt_bind_param($s, 'iisdi',$sid,$sub,$exam,$marks,$mid);
        return mysqli_stmt_execute($s);
    }
function teacher_marks(int $tid,int $cid):array {
        if(!teacher_ownsClass($tid,$cid))return[];
        $s=mysqli_prepare(db(), 'SELECT m.id,st.id AS student_pk,st.student_id,u.name,sub.subject_name,m.exam,m.marks FROM marks m JOIN students st ON st.id=m.student_id JOIN users u ON u.id=st.user_id JOIN subjects sub ON sub.id=m.subject_id WHERE st.class_id=? ORDER BY u.name,sub.subject_name,m.exam');
        mysqli_stmt_bind_param($s, 'i',$cid);
        mysqli_stmt_execute($s);
        return mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
    }
function teacher_availability(int $tid):array {
        $s=mysqli_prepare(db(), 'SELECT * FROM teacher_availability WHERE teacher_id=? ORDER BY FIELD(day,"Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"),from_time');
        mysqli_stmt_bind_param($s, 'i',$tid);
        mysqli_stmt_execute($s);
        return mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
    }
function teacher_saveAvailability(int $tid,string $day,string $from,string $to):bool {
        $days=['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        if(!in_array($day,$days,true)||!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/',$from)||!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/',$to)||$from>=$to)return false;
        $s=mysqli_prepare(db(), 'SELECT id FROM teacher_availability WHERE teacher_id=? AND day=? AND from_time=?');
        mysqli_stmt_bind_param($s, 'iss',$tid,$day,$from);
        mysqli_stmt_execute($s);
        $row=mysqli_fetch_assoc(mysqli_stmt_get_result($s));
        if($row) {
            $u=mysqli_prepare(db(), 'UPDATE teacher_availability SET to_time=? WHERE id=? AND teacher_id=?');
            mysqli_stmt_bind_param($u, 'sii',$to,$row['id'],$tid);
            return mysqli_stmt_execute($u);
        }
        $i=mysqli_prepare(db(), 'INSERT INTO teacher_availability(teacher_id,day,from_time,to_time) VALUES(?,?,?,?)');
        mysqli_stmt_bind_param($i, 'isss',$tid,$day,$from,$to);
        return mysqli_stmt_execute($i);
    }
function teacher_deleteAvailability(int $tid,int $id):bool {
        $s=mysqli_prepare(db(), 'DELETE FROM teacher_availability WHERE id=? AND teacher_id=?');
        mysqli_stmt_bind_param($s, 'ii',$id,$tid);
        return mysqli_stmt_execute($s);
    }

