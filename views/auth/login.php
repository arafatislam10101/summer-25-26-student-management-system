<div class="auth-page">
<div class="auth-shell">
<section class="auth-brand">
<div class="brand-mark">🎓</div>
<h1>EduManage</h1>
<p>A modern Student Management System for administrators, teachers, students and parents.</p>
<div class="auth-features">
<div class="auth-feature">
<b>✓</b> Manage students & teachers</div>
<div class="auth-feature">
<b>✓</b> Attendance & test results</div>
<div class="auth-feature">
<b>✓</b> Requests, notices & messaging</div>
<div class="auth-feature">
<b>✓</b> Secure role-based access</div>
</div>
</section>
<section class="auth-form">
<h2>Welcome back</h2>
<p class="muted">Sign in to continue to your dashboard.</p>
<form method="post" action="index.php?page=login/authenticate">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<div class="form-group">
<label>Email address</label>
<input type="email" name="email" value="<?=e($_COOKIE['remember_email']??'')?>" autocomplete="username" placeholder="you@example.com" required>
</div>
<div class="form-group">
<label>Password</label>
<input type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
</div>
<label style="display:flex;align-items:center;gap:8px;margin:12px 0 18px">
<input type="checkbox" name="remember" value="1"> Remember my email</label>
<button type="submit" style="width:100%">Sign in →</button>
</form>
<p style="text-align:center;margin-top:18px">New here? <a href="index.php?page=signup">Create an account</a></p>
<div class="demo-box">
<strong>Demo account</strong>
<br>admin@example.com · Admin@123</div>
</section>
</div>
</div>
