<div class="hero">
<div class="hero-content">
<h1>Welcome, <?=e($_SESSION['user']['name']??'Parent')?> 👋</h1>
<p>Monitor your child's attendance, results, requests, payments and performance in one place.</p>
</div>
</div>
<div class="profile-page">
<div class="profile-cover">
<img src="assets/images/profiles/student.svg" alt="Student profile photo">
<h2>
<?=e($child['student_name'])?>
</h2>
<p>
<?=e($child['student_id'])?>
</p>
</div>
<div class="card">
<div class="section-head">
<h2>Child overview</h2>
<span class="badge success">Active</span>
</div>
<div class="profile-info">
<div class="info-item">
<span>Student ID</span>
<strong>
<?=e($child['student_id'])?>
</strong>
</div>
<div class="info-item">
<span>Class</span>
<strong>
<?=e(($child['class_name']??'').' '.($child['section']??''))?>
</strong>
</div>
</div>
</div>
</div>
<div class="section-head">
<h2>Parent services</h2>
</div>
<div class="links">
<a href="index.php?page=parent/attendance">🗓️ Child Attendance</a>
<a href="index.php?page=parent/results">📈 Results / Performance</a>
<a href="index.php?page=parent/at-risk">⚠️ At-Risk Alerts</a>
<a href="index.php?page=parent/notices">📢 Notices & Accounts</a>
<a href="index.php?page=parent/payment">💳 Payment System</a>
<a href="index.php?page=parent/requests">📦 Request Package</a>
<a href="index.php?page=parent/rating">⭐ Rating / Feedback</a>
<a href="index.php?page=messages">✉️ Messaging</a>
</div>
