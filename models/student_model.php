<?php
require_once __DIR__.'/model.php';
function student_byUser(int $uid):?array {
        $s=mysqli_prepare(db(), 'SELECT s.*,u.name,u.email,u.status,c.class_name,c.section,COALESCE(pu.name,"No parent") parent_name FROM students s JOIN users u ON u.id=s.user_id LEFT JOIN classes c ON c.id=s.class_id LEFT JOIN parents p ON p.id=s.parent_id LEFT JOIN users pu ON pu.id=p.user_id WHERE s.user_id=?');
        mysqli_stmt_bind_param($s, 'i',$uid);
        mysqli_stmt_execute($s);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($s))?:null;
    }
function student_attendance(int $sid,string $from='',string $to=''):array {
        $sql='SELECT date,status FROM attendance WHERE student_id=?';
        if($from&&$to)$sql.=' AND date BETWEEN ? AND ?';
        $sql.=' ORDER BY date DESC';
        $s=mysqli_prepare(db(), $sql);
        if($from&&$to)mysqli_stmt_bind_param($s, 'iss',$sid,$from,$to);
        else mysqli_stmt_bind_param($s, 'i',$sid);
        mysqli_stmt_execute($s);
        return mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
    }
function student_attendanceSummary(int $sid):array {
        $s=mysqli_prepare(db(), "SELECT COUNT(*) total,SUM(status='Present') present,SUM(status='Late') late,SUM(status='Absent') absent FROM attendance WHERE student_id=?");
        mysqli_stmt_bind_param($s, 'i',$sid);
        mysqli_stmt_execute($s);
        $r=mysqli_fetch_assoc(mysqli_stmt_get_result($s));
        $r['percentage']=$r['total']?round((($r['present']+$r['late'])/$r['total'])*100,2):0;
        return$r;
    }
function student_results(int $sid):array {
        $s=mysqli_prepare(db(), 'SELECT sub.subject_name,m.exam,m.marks FROM marks m JOIN subjects sub ON sub.id=m.subject_id WHERE m.student_id=? ORDER BY sub.subject_name,m.exam');
        mysqli_stmt_bind_param($s, 'i',$sid);
        mysqli_stmt_execute($s);
        return mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
    }
function student_notices():array {
        return db_fetch_all('SELECT * FROM notices ORDER BY id DESC');
    }
function student_teacherAvailability():array {
        return db_fetch_all('SELECT u.name AS teacher_name,t.teacher_id,t.subject,ta.day,ta.from_time,ta.to_time FROM teacher_availability ta JOIN teachers t ON t.id=ta.teacher_id JOIN users u ON u.id=t.user_id WHERE u.status="active" ORDER BY u.name,FIELD(ta.day,"Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"),ta.from_time');
    }
function student_leave(int $sid,string $reason,string $from,string $to):bool {
        if(!valid_date($from)||!valid_date($to)||$from>$to)return false;
        $s=mysqli_prepare(db(), 'INSERT INTO leave_requests(student_id,reason,from_date,to_date) VALUES(?,?,?,?)');
        mysqli_stmt_bind_param($s, 'isss',$sid,$reason,$from,$to);
        return mysqli_stmt_execute($s);
    }
function student_online(int $sid,string $type,string $reason):bool {
        if(!in_array($type,['Online','Offline'],true))return false;
        $s=mysqli_prepare(db(), 'INSERT INTO online_requests(student_id,request_type,reason) VALUES(?,?,?)');
        mysqli_stmt_bind_param($s, 'iss',$sid,$type,$reason);
        return mysqli_stmt_execute($s);
    }
function student_requests(int $sid):array {

    $s=mysqli_prepare(db(), "
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

    mysqli_stmt_bind_param($s, 'ii',$sid,$sid);

    mysqli_stmt_execute($s);

    return mysqli_fetch_all(mysqli_stmt_get_result($s), MYSQLI_ASSOC);
}
function student_updateProfile(int $userId,string $name,string $email,string $phone,string $address):bool {
        mysqli_begin_transaction(db());

        try {
            $s=mysqli_prepare(db(), 'UPDATE users SET name=?,email=? WHERE id=?');
            mysqli_stmt_bind_param($s, 'ssi',$name,$email,$userId);
            mysqli_stmt_execute($s);

            $s=mysqli_prepare(db(), 'UPDATE students SET phone=?,address=? WHERE user_id=?');
            mysqli_stmt_bind_param($s, 'ssi',$phone,$address,$userId);
            mysqli_stmt_execute($s);

            mysqli_commit(db());
            return true;

        } catch(Throwable $e) {
            mysqli_rollback(db());
            return false;
        }
    }
function student_requestById(int $id,string $type):?array {

    if(str_starts_with($type,'Online/')) {

        $s=mysqli_prepare(db(), 
            'SELECT id,request_type,reason,status,NULL from_date,NULL to_date 
             FROM online_requests 
             WHERE id=?'
        );

    } else {

        $s=mysqli_prepare(db(), 
            'SELECT id,"Leave" type,reason,status,from_date,to_date 
             FROM leave_requests 
             WHERE id=?'
        );

    }


    mysqli_stmt_bind_param($s, 'i',$id);

    mysqli_stmt_execute($s);

    return mysqli_fetch_assoc(mysqli_stmt_get_result($s))?:null;

}
function student_updateRequest(
    int $id,
    string $type,
    string $reason,
    string $from='',
    string $to=''
):bool {


    if(str_starts_with($type,'Online/')) {


        $s=mysqli_prepare(db(), 
            'UPDATE online_requests 
             SET reason=? 
             WHERE id=?'
        );


        mysqli_stmt_bind_param($s, 'si',$reason,$id);


    } else {


        if(!valid_date($from)||!valid_date($to)||$from>$to) {
            return false;
        }


        $s=mysqli_prepare(db(), 
            'UPDATE leave_requests 
             SET reason=?,from_date=?,to_date=? 
             WHERE id=?'
        );


        mysqli_stmt_bind_param($s, 'sssi',$reason,$from,$to,$id);

    }


    return mysqli_stmt_execute($s);

}
function student_deleteRequest(int $id,string $type):bool {


    if(str_starts_with($type,'Online/')) {


        $s=mysqli_prepare(db(), 
            'DELETE FROM online_requests 
             WHERE id=?'
        );


    } else {


        $s=mysqli_prepare(db(), 
            'DELETE FROM leave_requests 
             WHERE id=?'
        );

    }


    mysqli_stmt_bind_param($s, 'i',$id);

    return mysqli_stmt_execute($s);

}

