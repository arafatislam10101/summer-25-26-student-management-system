<?php
require_once __DIR__.'/model.php';
class Admin extends Model {
    function stats():array {
        $out=[];
        foreach(['students','teachers','classes','notices','leave_requests','online_requests','package_requests','messages'] as $t) {
            $r=$this->db->query("SELECT COUNT(*) c FROM $t");
            $out[$t]=(int)$r->fetch_assoc()['c'];
        }
        return $out;
    }
    function students():array {
        $r=$this->db->query("SELECT s.id,s.user_id,s.student_id,u.name,u.email,u.status,s.phone,s.address,c.class_name,c.section,COALESCE(pu.name,'—') parent_name FROM students s JOIN users u ON u.id=s.user_id LEFT JOIN classes c ON c.id=s.class_id LEFT JOIN parents p ON p.id=s.parent_id LEFT JOIN users pu ON pu.id=p.user_id ORDER BY s.id DESC");
        return $r?$r->fetch_all(MYSQLI_ASSOC):[];
    }
    function student(int $id):?array {
        $s=$this->db->prepare('SELECT s.*,u.name,u.email FROM students s JOIN users u ON u.id=s.user_id WHERE s.id=?');
        $s->bind_param('i',$id);
        $s->execute();
        return $s->get_result()->fetch_assoc()?:null;
    }
    function teachers():array {
        $r=$this->db->query("SELECT t.id,t.user_id,t.teacher_id,u.name,u.email,u.status,t.phone,t.subject,t.background FROM teachers t JOIN users u ON u.id=t.user_id ORDER BY t.id DESC");
        return $r?$r->fetch_all(MYSQLI_ASSOC):[];
    }
    function teacher(int $id):?array {
        $s=$this->db->prepare('SELECT t.*,u.name,u.email FROM teachers t JOIN users u ON u.id=t.user_id WHERE t.id=?');
        $s->bind_param('i',$id);
        $s->execute();
        return $s->get_result()->fetch_assoc()?:null;
    }
    function classes():array {
        $sql="SELECT c.*,COALESCE(u.name,'Unassigned') teacher_name FROM classes c LEFT JOIN teachers t ON t.id=c.teacher_id LEFT JOIN users u ON u.id=t.user_id ORDER BY c.id DESC";
        $r=$this->db->query($sql);
        return $r?$r->fetch_all(MYSQLI_ASSOC):[];
    }
    function subjects():array {
        $r=$this->db->query("SELECT s.*,c.class_name,c.section FROM subjects s LEFT JOIN classes c ON c.id=s.class_id ORDER BY s.id DESC");
        return $r?$r->fetch_all(MYSQLI_ASSOC):[];
    }
    function notices():array {
        $r=$this->db->query('SELECT * FROM notices ORDER BY id DESC');
        return $r?$r->fetch_all(MYSQLI_ASSOC):[];
    }
    function parents():array {
        $r=$this->db->query("SELECT p.id,u.name FROM parents p JOIN users u ON u.id=p.user_id ORDER BY u.name");
        return $r?$r->fetch_all(MYSQLI_ASSOC):[];
    }
    function createUserStudent(array $d):array {
        $this->db->begin_transaction();
        try {
            $h=password_hash($d['password'],PASSWORD_DEFAULT);
            $s=$this->db->prepare('INSERT INTO users(name,email,password,role) VALUES(?,?,?,\'student\')');
            $s->bind_param('sss',$d['name'],$d['email'],$h);
            $s->execute();
            $uid=$this->db->insert_id;
            $s=$this->db->prepare('INSERT INTO students(user_id,student_id,phone,address,class_id,parent_id) VALUES(?,?,?,?,NULLIF(?,0),NULLIF(?,0))');
            $s->bind_param('isssii',$uid,$d['student_id'],$d['phone'],$d['address'],$d['class_id'],$d['parent_id']);
            $s->execute();
            $this->db->commit();
            return[true,'Student created.'];
} catch (Throwable $e) {
            $this->db->rollback();
            return[false,'Unable to create student. Check duplicate email/ID.'];
        }
    }
    function updateStudent(array $d):array {
        $this->db->begin_transaction();
        try {
            $s=$this->db->prepare('UPDATE users SET name=?,email=? WHERE id=? AND role=\'student\'');
            $s->bind_param('ssi',$d['name'],$d['email'],$d['user_id']);
            $s->execute();
            $s=$this->db->prepare('UPDATE students SET student_id=?,phone=?,address=?,class_id=NULLIF(?,0),parent_id=NULLIF(?,0) WHERE id=?');
            $s->bind_param('sssiii',$d['student_id'],$d['phone'],$d['address'],$d['class_id'],$d['parent_id'],$d['id']);
            $s->execute();
            $this->db->commit();
            return[true,'Student updated.'];
} catch (Throwable $e) {
            $this->db->rollback();
            return[false,'Unable to update student. Check duplicate email/ID.'];
        }
    }
    function createUserTeacher(array $d):array {
        $this->db->begin_transaction();
        try {
            $h=password_hash($d['password'],PASSWORD_DEFAULT);
            $s=$this->db->prepare('INSERT INTO users(name,email,password,role) VALUES(?,?,?,\'teacher\')');
            $s->bind_param('sss',$d['name'],$d['email'],$h);
            $s->execute();
            $uid=$this->db->insert_id;
            $s=$this->db->prepare('INSERT INTO teachers(user_id,teacher_id,phone,subject,background) VALUES(?,?,?,?,?)');
            $s->bind_param('issss',$uid,$d['teacher_id'],$d['phone'],$d['subject'],$d['background']);
            $s->execute();
            $this->db->commit();
            return[true,'Teacher created.'];
} catch (Throwable $e) {
            $this->db->rollback();
            return[false,'Unable to create teacher. Check duplicate email/ID.'];
        }
    }
    function updateTeacherBackground(int $id,string $background):bool {
        $s=$this->db->prepare('UPDATE teachers SET background=? WHERE id=?');
        $s->bind_param('si',$background,$id);
        return $s->execute();
    }
    function updateTeacher(array $d):array {
        $this->db->begin_transaction();
        try {
            $s=$this->db->prepare('UPDATE users SET name=?,email=? WHERE id=? AND role=\'teacher\'');
            $s->bind_param('ssi',$d['name'],$d['email'],$d['user_id']);
            $s->execute();
            $s=$this->db->prepare('UPDATE teachers SET teacher_id=?,phone=?,subject=?,background=? WHERE id=?');
            $s->bind_param('ssssi',$d['teacher_id'],$d['phone'],$d['subject'],$d['background'],$d['id']);
            $s->execute();
            $this->db->commit();
            return[true,'Teacher updated.'];
} catch (Throwable $e) {
            $this->db->rollback();
            return[false,'Unable to update teacher.'];
        }
    }
    function deleteStudent(int $id):bool {
        $s=$this->db->prepare('SELECT user_id FROM students WHERE id=?');
        $s->bind_param('i',$id);
        $s->execute();
        $u=$s->get_result()->fetch_assoc();
        if(!$u)return false;
        $s=$this->db->prepare('DELETE FROM users WHERE id=?');
        $s->bind_param('i',$u['user_id']);
        return $s->execute();
    }
    function deleteTeacher(int $id):bool {
        $s=$this->db->prepare('SELECT user_id FROM teachers WHERE id=?');
        $s->bind_param('i',$id);
        $s->execute();
        $u=$s->get_result()->fetch_assoc();
        if(!$u)return false;
        $s=$this->db->prepare('DELETE FROM users WHERE id=?');
        $s->bind_param('i',$u['user_id']);
        return $s->execute();
    }
    function saveClassWithSubject(int $classId,string $className,string $section,int $teacherId,int $subjectId,string $subjectName):bool {
        $teacherId=max(0,$teacherId);
        $subjectId=max(0,$subjectId);
        $className=trim($className);
        $section=trim($section);
        $subjectName=trim($subjectName);
        $this->db->begin_transaction();
        try {
            if($classId) {
                $s=$this->db->prepare('UPDATE classes SET class_name=?,section=?,teacher_id=NULLIF(?,0) WHERE id=?');
                $s->bind_param('ssii',$className,$section,$teacherId,$classId);
                $s->execute();
                $cid=$classId;
            } else {
                $s=$this->db->prepare('INSERT INTO classes(class_name,section,teacher_id) VALUES(?,?,NULLIF(?,0))');
                $s->bind_param('ssi',$className,$section,$teacherId);
                $s->execute();
                $cid=$this->db->insert_id;
            }

            // One teacher per class; one teacher can teach many classes.
            $d=$this->db->prepare('DELETE FROM teacher_classes WHERE class_id=?');
            $d->bind_param('i',$cid);
            $d->execute();
            if($teacherId>0) {
                $i=$this->db->prepare('INSERT INTO teacher_classes(teacher_id,class_id) VALUES(?,?)');
                $i->bind_param('ii',$teacherId,$cid);
                $i->execute();
            }

            // Create or update a subject together with the class.
            if($subjectName!=='') {
                if($subjectId>0) {
                    $q=$this->db->prepare('UPDATE subjects SET subject_name=?,class_id=? WHERE id=?');
                    $q->bind_param('sii',$subjectName,$cid,$subjectId);
                    $q->execute();
                } else {
                    $q=$this->db->prepare('INSERT INTO subjects(subject_name,class_id) VALUES(?,?)');
                    $q->bind_param('si',$subjectName,$cid);
                    $q->execute();
                }
            }

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollback();
            return false;
        }
    }

    function saveClass(int $id,string $n,string $sec,int $teacherId):bool {
        $teacherId=max(0,$teacherId);
        $this->db->begin_transaction();
        try {
            if($id) {
                $s=$this->db->prepare('UPDATE classes SET class_name=?,section=?,teacher_id=NULLIF(?,0) WHERE id=?');
                $s->bind_param('ssii',$n,$sec,$teacherId,$id);
                $s->execute();
                $cid=$id;
            } else {
                $s=$this->db->prepare('INSERT INTO classes(class_name,section,teacher_id) VALUES(?,?,NULLIF(?,0))');
                $s->bind_param('ssi',$n,$sec,$teacherId);
                $s->execute();
                $cid=$this->db->insert_id;
            }

            // Keep the legacy mapping table synchronized, but allow only
            // one teacher per class. The same teacher may teach many classes.
            $d=$this->db->prepare('DELETE FROM teacher_classes WHERE class_id=?');
            $d->bind_param('i',$cid);
            $d->execute();

            if($teacherId>0) {
                $i=$this->db->prepare('INSERT INTO teacher_classes(teacher_id,class_id) VALUES(?,?)');
                $i->bind_param('ii',$teacherId,$cid);
                $i->execute();
            }

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollback();
            return false;
        }
    }
    function deleteClass(int $id):bool {
        $s=$this->db->prepare('DELETE FROM classes WHERE id=?');
        $s->bind_param('i',$id);
        return $s->execute();
    }
    function saveSubject(int $id,string $n,int $cid):bool {
        $sql=$id?'UPDATE subjects SET subject_name=?,class_id=NULLIF(?,0) WHERE id=?':'INSERT INTO subjects(subject_name,class_id) VALUES(?,NULLIF(?,0))';
        $s=$this->db->prepare($sql);
        if($id)$s->bind_param('sii',$n,$cid,$id);
        else $s->bind_param('si',$n,$cid);
        return $s->execute();
    }
    function deleteSubject(int $id):bool {
        $s=$this->db->prepare('DELETE FROM subjects WHERE id=?');
        $s->bind_param('i',$id);
        return $s->execute();
    }
    function saveNotice(int $id,string $t,string $d):bool {
        $sql=$id?'UPDATE notices SET title=?,description=? WHERE id=?':'INSERT INTO notices(title,description) VALUES(?,?)';
        $s=$this->db->prepare($sql);
        if($id)$s->bind_param('ssi',$t,$d,$id);
        else$s->bind_param('ss',$t,$d);
        return$s->execute();
    }
    function deleteNotice(int $id):bool {
        $s=$this->db->prepare('DELETE FROM notices WHERE id=?');
        $s->bind_param('i',$id);
        return$s->execute();
    }
    function toggle(int $id):bool {
        $s=$this->db->prepare("UPDATE users SET status=IF(status='active','inactive','active') WHERE id=? AND role<>'admin'");
        $s->bind_param('i',$id);
        return$s->execute();
    }
    function requests():array {
        $sql="SELECT l.id,'Leave' type,u.name,CONCAT(l.from_date,' to ',l.to_date,' — ',l.reason) details,l.status,l.id request_id FROM leave_requests l JOIN students s ON s.id=l.student_id JOIN users u ON u.id=s.user_id UNION ALL SELECT o.id,CONCAT('Online/',o.request_type),u.name,o.reason,o.status,o.id FROM online_requests o JOIN students s ON s.id=o.student_id JOIN users u ON u.id=s.user_id UNION ALL SELECT p.id,'Package',u.name,CONCAT(p.package_name,' — ',COALESCE(p.reason,'')),p.status,p.id FROM package_requests p JOIN students s ON s.id=p.student_id JOIN users u ON u.id=s.user_id ORDER BY request_id DESC";
        $r=$this->db->query($sql);
        return$r?$r->fetch_all(MYSQLI_ASSOC):[];
    }
    function setRequest(string $type,int $id,string $status):bool {
        $table=str_starts_with($type,'Online/')?'online_requests':($type==='Leave'?'leave_requests':'package_requests');
        if(!in_array($status,['Approved','Rejected'],true))return false;
        $s=$this->db->prepare("UPDATE $table SET status=? WHERE id=?");
        $s->bind_param('si',$status,$id);
        return$s->execute();
    }
    function feedback():array {
        $r=$this->db->query("SELECT r.*,s.student_id,u.name student_name,ROUND(r.rating,1) rating FROM ratings r JOIN students s ON s.id=r.student_id JOIN users u ON u.id=s.user_id ORDER BY r.id DESC");
        return$r?$r->fetch_all(MYSQLI_ASSOC):[];
    }
}
