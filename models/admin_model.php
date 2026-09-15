<?php
require_once __DIR__.'/model.php';
function admin_stats():array {
        $out=[];
        foreach(['students','teachers','classes','notices','leave_requests','online_requests','package_requests','messages'] as $t) {
            $row=db_fetch_one("SELECT COUNT(*) c FROM $t");
            $out[$t]=(int)($row['c'] ?? 0);
        }
        return $out;
    }
function admin_students():array {
        return db_fetch_all("SELECT s.id,s.user_id,s.student_id,u.name,u.email,u.status,s.phone,s.address,c.class_name,c.section,COALESCE(pu.name,'—') parent_name FROM students s JOIN users u ON u.id=s.user_id LEFT JOIN classes c ON c.id=s.class_id LEFT JOIN parents p ON p.id=s.parent_id LEFT JOIN users pu ON pu.id=p.user_id ORDER BY s.id DESC");
    }
function admin_student(int $id):?array {
        $s=mysqli_prepare(db(), 'SELECT s.*,u.name,u.email FROM students s JOIN users u ON u.id=s.user_id WHERE s.id=?');
        mysqli_stmt_bind_param($s, 'i',$id);
        mysqli_stmt_execute($s);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($s))?:null;
    }
function admin_teachers():array {
        return db_fetch_all("SELECT t.id,t.user_id,t.teacher_id,u.name,u.email,u.status,t.phone,t.subject,t.background FROM teachers t JOIN users u ON u.id=t.user_id ORDER BY t.id DESC");
    }
function admin_teacher(int $id):?array {
        $s=mysqli_prepare(db(), 'SELECT t.*,u.name,u.email FROM teachers t JOIN users u ON u.id=t.user_id WHERE t.id=?');
        mysqli_stmt_bind_param($s, 'i',$id);
        mysqli_stmt_execute($s);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($s))?:null;
    }
function admin_classes():array {
        $sql="SELECT c.*,COALESCE(u.name,'Unassigned') teacher_name FROM classes c LEFT JOIN teachers t ON t.id=c.teacher_id LEFT JOIN users u ON u.id=t.user_id ORDER BY c.id DESC";
        return db_fetch_all($sql);
    }
function admin_subjects():array {
        return db_fetch_all("SELECT s.*,c.class_name,c.section FROM subjects s LEFT JOIN classes c ON c.id=s.class_id ORDER BY s.id DESC");
    }
function admin_notices():array {
        return db_fetch_all('SELECT * FROM notices ORDER BY id DESC');
    }
function admin_parents():array {
        return db_fetch_all("SELECT p.id,u.name FROM parents p JOIN users u ON u.id=p.user_id ORDER BY u.name");
    }
function admin_createUserStudent(array $d):array {
        mysqli_begin_transaction(db());
        try {
            $h=password_hash($d['password'],PASSWORD_DEFAULT);
            $s=mysqli_prepare(db(), 'INSERT INTO users(name,email,password,role) VALUES(?,?,?,\'student\')');
            mysqli_stmt_bind_param($s, 'sss',$d['name'],$d['email'],$h);
            mysqli_stmt_execute($s);
            $uid=mysqli_insert_id(db());
            $s=mysqli_prepare(db(), 'INSERT INTO students(user_id,student_id,phone,address,class_id,parent_id) VALUES(?,?,?,?,NULLIF(?,0),NULLIF(?,0))');
            mysqli_stmt_bind_param($s, 'isssii',$uid,$d['student_id'],$d['phone'],$d['address'],$d['class_id'],$d['parent_id']);
            mysqli_stmt_execute($s);
            mysqli_commit(db());
            return[true,'Student created.'];
} catch (Throwable $e) {
            mysqli_rollback(db());
            return[false,'Unable to create student. Check duplicate email/ID.'];
        }
    }
function admin_updateStudent(array $d):array {
        mysqli_begin_transaction(db());
        try {
            $s=mysqli_prepare(db(), 'UPDATE users SET name=?,email=? WHERE id=? AND role=\'student\'');
            mysqli_stmt_bind_param($s, 'ssi',$d['name'],$d['email'],$d['user_id']);
            mysqli_stmt_execute($s);
            $s=mysqli_prepare(db(), 'UPDATE students SET student_id=?,phone=?,address=?,class_id=NULLIF(?,0),parent_id=NULLIF(?,0) WHERE id=?');
            mysqli_stmt_bind_param($s, 'sssiii',$d['student_id'],$d['phone'],$d['address'],$d['class_id'],$d['parent_id'],$d['id']);
            mysqli_stmt_execute($s);
            mysqli_commit(db());
            return[true,'Student updated.'];
} catch (Throwable $e) {
            mysqli_rollback(db());
            return[false,'Unable to update student. Check duplicate email/ID.'];
        }
    }
function admin_createUserTeacher(array $d):array {
        mysqli_begin_transaction(db());
        try {
            $h=password_hash($d['password'],PASSWORD_DEFAULT);
            $s=mysqli_prepare(db(), 'INSERT INTO users(name,email,password,role) VALUES(?,?,?,\'teacher\')');
            mysqli_stmt_bind_param($s, 'sss',$d['name'],$d['email'],$h);
            mysqli_stmt_execute($s);
            $uid=mysqli_insert_id(db());
            $s=mysqli_prepare(db(), 'INSERT INTO teachers(user_id,teacher_id,phone,subject,background) VALUES(?,?,?,?,?)');
            mysqli_stmt_bind_param($s, 'issss',$uid,$d['teacher_id'],$d['phone'],$d['subject'],$d['background']);
            mysqli_stmt_execute($s);
            mysqli_commit(db());
            return[true,'Teacher created.'];
} catch (Throwable $e) {
            mysqli_rollback(db());
            return[false,'Unable to create teacher. Check duplicate email/ID.'];
        }
    }
function admin_updateTeacherBackground(int $id,string $background):bool {
        $s=mysqli_prepare(db(), 'UPDATE teachers SET background=? WHERE id=?');
        mysqli_stmt_bind_param($s, 'si',$background,$id);
        return mysqli_stmt_execute($s);
    }
function admin_updateTeacher(array $d):array {
        mysqli_begin_transaction(db());
        try {
            $s=mysqli_prepare(db(), 'UPDATE users SET name=?,email=? WHERE id=? AND role=\'teacher\'');
            mysqli_stmt_bind_param($s, 'ssi',$d['name'],$d['email'],$d['user_id']);
            mysqli_stmt_execute($s);
            $s=mysqli_prepare(db(), 'UPDATE teachers SET teacher_id=?,phone=?,subject=?,background=? WHERE id=?');
            mysqli_stmt_bind_param($s, 'ssssi',$d['teacher_id'],$d['phone'],$d['subject'],$d['background'],$d['id']);
            mysqli_stmt_execute($s);
            mysqli_commit(db());
            return[true,'Teacher updated.'];
} catch (Throwable $e) {
            mysqli_rollback(db());
            return[false,'Unable to update teacher.'];
        }
    }
function admin_deleteStudent(int $id):bool {
        $s=mysqli_prepare(db(), 'SELECT user_id FROM students WHERE id=?');
        mysqli_stmt_bind_param($s, 'i',$id);
        mysqli_stmt_execute($s);
        $u=mysqli_fetch_assoc(mysqli_stmt_get_result($s));
        if(!$u)return false;
        $s=mysqli_prepare(db(), 'DELETE FROM users WHERE id=?');
        mysqli_stmt_bind_param($s, 'i',$u['user_id']);
        return mysqli_stmt_execute($s);
    }
function admin_deleteTeacher(int $id):bool {
        $s=mysqli_prepare(db(), 'SELECT user_id FROM teachers WHERE id=?');
        mysqli_stmt_bind_param($s, 'i',$id);
        mysqli_stmt_execute($s);
        $u=mysqli_fetch_assoc(mysqli_stmt_get_result($s));
        if(!$u)return false;
        $s=mysqli_prepare(db(), 'DELETE FROM users WHERE id=?');
        mysqli_stmt_bind_param($s, 'i',$u['user_id']);
        return mysqli_stmt_execute($s);
    }
function admin_saveClassWithSubject(int $classId,string $className,string $section,int $teacherId,int $subjectId,string $subjectName):bool {
        $teacherId=max(0,$teacherId);
        $subjectId=max(0,$subjectId);
        $className=trim($className);
        $section=trim($section);
        $subjectName=trim($subjectName);
        mysqli_begin_transaction(db());
        try {
            if($classId) {
                $s=mysqli_prepare(db(), 'UPDATE classes SET class_name=?,section=?,teacher_id=NULLIF(?,0) WHERE id=?');
                mysqli_stmt_bind_param($s, 'ssii',$className,$section,$teacherId,$classId);
                mysqli_stmt_execute($s);
                $cid=$classId;
            } else {
                $s=mysqli_prepare(db(), 'INSERT INTO classes(class_name,section,teacher_id) VALUES(?,?,NULLIF(?,0))');
                mysqli_stmt_bind_param($s, 'ssi',$className,$section,$teacherId);
                mysqli_stmt_execute($s);
                $cid=mysqli_insert_id(db());
            }

            // One teacher per class; one teacher can teach many classes.
            $d=mysqli_prepare(db(), 'DELETE FROM teacher_classes WHERE class_id=?');
            mysqli_stmt_bind_param($d, 'i',$cid);
            mysqli_stmt_execute($d);
            if($teacherId>0) {
                $i=mysqli_prepare(db(), 'INSERT INTO teacher_classes(teacher_id,class_id) VALUES(?,?)');
                mysqli_stmt_bind_param($i, 'ii',$teacherId,$cid);
                mysqli_stmt_execute($i);
            }

            // Create or update a subject together with the class.
            if($subjectName!=='') {
                if($subjectId>0) {
                    $q=mysqli_prepare(db(), 'UPDATE subjects SET subject_name=?,class_id=? WHERE id=?');
                    mysqli_stmt_bind_param($q, 'sii',$subjectName,$cid,$subjectId);
                    mysqli_stmt_execute($q);
                } else {
                    $q=mysqli_prepare(db(), 'INSERT INTO subjects(subject_name,class_id) VALUES(?,?)');
                    mysqli_stmt_bind_param($q, 'si',$subjectName,$cid);
                    mysqli_stmt_execute($q);
                }
            }

            mysqli_commit(db());
            return true;
        } catch (Throwable $e) {
            mysqli_rollback(db());
            return false;
        }
    }
function admin_saveClass(int $id,string $n,string $sec,int $teacherId):bool {
        $teacherId=max(0,$teacherId);
        mysqli_begin_transaction(db());
        try {
            if($id) {
                $s=mysqli_prepare(db(), 'UPDATE classes SET class_name=?,section=?,teacher_id=NULLIF(?,0) WHERE id=?');
                mysqli_stmt_bind_param($s, 'ssii',$n,$sec,$teacherId,$id);
                mysqli_stmt_execute($s);
                $cid=$id;
            } else {
                $s=mysqli_prepare(db(), 'INSERT INTO classes(class_name,section,teacher_id) VALUES(?,?,NULLIF(?,0))');
                mysqli_stmt_bind_param($s, 'ssi',$n,$sec,$teacherId);
                mysqli_stmt_execute($s);
                $cid=mysqli_insert_id(db());
            }

            // Keep the legacy mapping table synchronized, but allow only
            // one teacher per class. The same teacher may teach many classes.
            $d=mysqli_prepare(db(), 'DELETE FROM teacher_classes WHERE class_id=?');
            mysqli_stmt_bind_param($d, 'i',$cid);
            mysqli_stmt_execute($d);

            if($teacherId>0) {
                $i=mysqli_prepare(db(), 'INSERT INTO teacher_classes(teacher_id,class_id) VALUES(?,?)');
                mysqli_stmt_bind_param($i, 'ii',$teacherId,$cid);
                mysqli_stmt_execute($i);
            }

            mysqli_commit(db());
            return true;
        } catch (Throwable $e) {
            mysqli_rollback(db());
            return false;
        }
    }
function admin_deleteClass(int $id):bool {
        $s=mysqli_prepare(db(), 'DELETE FROM classes WHERE id=?');
        mysqli_stmt_bind_param($s, 'i',$id);
        return mysqli_stmt_execute($s);
    }
function admin_saveSubject(int $id,string $n,int $cid):bool {
        $sql=$id?'UPDATE subjects SET subject_name=?,class_id=NULLIF(?,0) WHERE id=?':'INSERT INTO subjects(subject_name,class_id) VALUES(?,NULLIF(?,0))';
        $s=mysqli_prepare(db(), $sql);
        if($id)mysqli_stmt_bind_param($s, 'sii',$n,$cid,$id);
        else mysqli_stmt_bind_param($s, 'si',$n,$cid);
        return mysqli_stmt_execute($s);
    }
function admin_deleteSubject(int $id):bool {
        $s=mysqli_prepare(db(), 'DELETE FROM subjects WHERE id=?');
        mysqli_stmt_bind_param($s, 'i',$id);
        return mysqli_stmt_execute($s);
    }
function admin_saveNotice(int $id,string $t,string $d):bool {
        $sql=$id?'UPDATE notices SET title=?,description=? WHERE id=?':'INSERT INTO notices(title,description) VALUES(?,?)';
        $s=mysqli_prepare(db(), $sql);
        if($id)mysqli_stmt_bind_param($s, 'ssi',$t,$d,$id);
        else mysqli_stmt_bind_param($s, 'ss',$t,$d);
        return mysqli_stmt_execute($s);
    }
function admin_deleteNotice(int $id):bool {
        $s=mysqli_prepare(db(), 'DELETE FROM notices WHERE id=?');
        mysqli_stmt_bind_param($s, 'i',$id);
        return mysqli_stmt_execute($s);
    }
function admin_toggle(int $id):bool {
        $s=mysqli_prepare(db(), "UPDATE users SET status=IF(status='active','inactive','active') WHERE id=? AND role<>'admin'");
        mysqli_stmt_bind_param($s, 'i',$id);
        return mysqli_stmt_execute($s);
    }
function admin_requests():array {
        $sql="SELECT l.id,'Leave' type,u.name,CONCAT(l.from_date,' to ',l.to_date,' — ',l.reason) details,l.status,l.id request_id FROM leave_requests l JOIN students s ON s.id=l.student_id JOIN users u ON u.id=s.user_id UNION ALL SELECT o.id,CONCAT('Online/',o.request_type),u.name,o.reason,o.status,o.id FROM online_requests o JOIN students s ON s.id=o.student_id JOIN users u ON u.id=s.user_id UNION ALL SELECT p.id,'Package',u.name,CONCAT(p.package_name,' — ',COALESCE(p.reason,'')),p.status,p.id FROM package_requests p JOIN students s ON s.id=p.student_id JOIN users u ON u.id=s.user_id ORDER BY request_id DESC";
        return db_fetch_all($sql);
    }
function admin_setRequest(string $type,int $id,string $status):bool {
        $table=str_starts_with($type,'Online/')?'online_requests':($type==='Leave'?'leave_requests':'package_requests');
        if(!in_array($status,['Approved','Rejected'],true))return false;
        $s=mysqli_prepare(db(), "UPDATE $table SET status=? WHERE id=?");
        mysqli_stmt_bind_param($s, 'si',$status,$id);
        return mysqli_stmt_execute($s);
    }
function admin_feedback():array {
        return db_fetch_all("SELECT r.*,s.student_id,u.name student_name,ROUND(r.rating,1) rating FROM ratings r JOIN students s ON s.id=r.student_id JOIN users u ON u.id=s.user_id ORDER BY r.id DESC");
    }

