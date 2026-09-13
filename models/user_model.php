<?php
require_once __DIR__.'/model.php';
class User extends Model {
    function findByEmail(string $email): ?array {
        $s=mysqli_prepare($this->db,'SELECT * FROM users WHERE email=? LIMIT 1');
        mysqli_stmt_bind_param($s,'s',$email);
        mysqli_stmt_execute($s);
        $r=mysqli_stmt_get_result($s)->fetch_assoc();
        return $r?:null;
    }
    function all(): array {
        $r=mysqli_query($this->db,'SELECT id,name,email,role,status,created_at FROM users ORDER BY id DESC');
        return $r?mysqli_fetch_all($r,MYSQLI_ASSOC):[];
    }
    function toggle(int $id): bool {
        $s=mysqli_prepare($this->db,"UPDATE users SET status=IF(status='active','inactive','active') WHERE id=?");
        mysqli_stmt_bind_param($s,'i',$id);
        return mysqli_stmt_execute($s);
    }
}
