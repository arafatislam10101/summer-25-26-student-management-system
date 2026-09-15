<div class="auth-page">
<div class="auth-shell signup-shell">
<section class="auth-brand">
<div class="brand-mark">🎓</div>
<h1>Join EduManage</h1>
<p>Create an account for your role and access the right dashboard.</p>
<div class="auth-features">
<div class="auth-feature"><b>✓</b> Student academic management</div>
<div class="auth-feature"><b>✓</b> Teacher classroom tools</div>
<div class="auth-feature"><b>✓</b> Parent monitoring & payments</div>
<div class="auth-feature"><b>✓</b> Secure role-based access</div>
</div>
</section>

<section class="auth-form">
<h2>Create account</h2>
<p class="muted">Choose your role and enter your information.</p>

<?php if($msg=flash()): ?>
<div class="alert"><?=e($msg)?></div>
<?php endif; ?>

<form method="post" action="index.php?page=signup/register" id="signupForm">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">

<div class="form-group">
<label>Account type</label>
<select name="role" id="role" required onchange="changeRoleFields()">
<option value="">Select role</option>
<option value="student" <?=old('role')==='student'?'selected':''?>>Student</option>
<option value="teacher" <?=old('role')==='teacher'?'selected':''?>>Teacher</option>
<option value="parent" <?=old('role')==='parent'?'selected':''?>>Parent</option>
</select>
</div>

<div class="gridform">
<div class="form-group">
<label>Full name</label>
<input type="text" name="name" value="<?=old('name')?>" placeholder="Enter full name" maxlength="100" required>
</div>

<div class="form-group">
<label>Email address</label>
<input type="email" name="email" value="<?=old('email')?>" placeholder="you@example.com" maxlength="150" autocomplete="email" required>
</div>

<div class="form-group">
<label>Password</label>
<input type="password" name="password" placeholder="At least 8 characters" minlength="8" autocomplete="new-password" required>
</div>

<div class="form-group">
<label>Confirm password</label>
<input type="password" name="confirm_password" placeholder="Repeat password" minlength="8" autocomplete="new-password" required>
</div>

<div class="form-group role-field student-field">
<label>Student ID</label>
<input type="text" name="student_id" value="<?=old('student_id')?>" placeholder="e.g. S-002" maxlength="30">
</div>

<div class="form-group role-field teacher-field">
<label>Teacher ID</label>
<input type="text" name="teacher_id" value="<?=old('teacher_id')?>" placeholder="e.g. T-002" maxlength="30">
</div>

<div class="form-group role-field contact-field">
<label>Phone</label>
<input type="text" name="phone" value="<?=old('phone')?>" placeholder="01XXXXXXXXX" maxlength="30">
</div>

<div class="form-group role-field teacher-field">
<label>Subject</label>
<input type="text" name="subject" value="<?=old('subject')?>" placeholder="e.g. Mathematics" maxlength="100">
</div>

<div class="form-group role-field student-field">
<label>Address</label>
<input type="text" name="address" value="<?=old('address')?>" placeholder="Enter address" maxlength="255">
</div>

<div class="form-group role-field teacher-field">
<label>Background</label>
<input type="text" name="background" value="<?=old('background')?>" placeholder="Qualification / experience">
</div>
</div>

<button type="submit" style="width:100%;margin-top:4px">Create account →</button>
</form>

<p style="text-align:center;margin-top:18px">
Already have an account? <a href="index.php?page=login">Sign in</a>
</p>
</section>
</div>
</div>

<script>
function changeRoleFields(){
    const role=document.getElementById('role').value;
    document.querySelectorAll('.role-field').forEach(el=>{
        el.style.display='none';
        el.querySelectorAll('input').forEach(i=>i.required=false);
    });

    if(role==='student'){
        document.querySelectorAll('.student-field').forEach(el=>el.style.display='block');
        document.querySelectorAll('.student-field input[name="student_id"]').forEach(i=>i.required=true);
    }
    if(role==='teacher'){
        document.querySelectorAll('.teacher-field').forEach(el=>el.style.display='block');
        document.querySelectorAll('.teacher-field input[name="teacher_id"]').forEach(i=>i.required=true);
    }
    if(role==='student' || role==='teacher' || role==='parent'){
        document.querySelectorAll('.contact-field').forEach(el=>el.style.display='block');
        document.querySelectorAll('.contact-field input').forEach(i=>i.required=true);
    }
}
document.addEventListener('DOMContentLoaded',changeRoleFields);
</script>
