<?php
require_once __DIR__.'/model.php';
function user_findByEmail(string $email): ?array {
        $s=mysqli_prepare(db(),'SELECT * FROM users WHERE email=? LIMIT 1');
        mysqli_stmt_bind_param($s,'s',$email);
        mysqli_stmt_execute($s);
        $r=mysqli_fetch_assoc(mysqli_stmt_get_result($s));
        return $r?:null;
    }
function user_all(): array {
        return db_fetch_all('SELECT id,name,email,role,status,created_at FROM users ORDER BY id DESC');
    }
function user_toggle(int $id): bool {
        $s=mysqli_prepare(db(),"UPDATE users SET status=IF(status='active','inactive','active') WHERE id=?");
        mysqli_stmt_bind_param($s,'i',$id);
        return mysqli_stmt_execute($s);
    }

