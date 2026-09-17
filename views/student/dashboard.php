<div class="hero">
<div class="hero-content">
<h1>Welcome, <?=e($student['name'])?> 👋</h1>
<p>Keep track of your academic progress, attendance, results and requests.</p>
</div>
</div>
<div class="grid">
<div class="card kpi">
<div>
<div class="muted">Student ID</div>
<h2>
<?=e($student['student_id'])?>
</h2>
<small>
<?=e(($student['class_name']??'No class').' '.($student['section']??''))?>
</small>
</div>
<div class="kpi-icon">🎓</div>
</div>
<div class="card kpi">
<div>
<div class="muted">Attendance</div>
<div class="stat">
<?=e($summary['percentage'])?>%</div>
</div>
<div class="kpi-icon">🗓️</div>
</div>
<div class="card kpi">
<div>
<div class="muted">Present</div>
<div class="stat">
<?=e($summary['present']??0)?>
</div>
</div>
<div class="kpi-icon">✓</div>
</div>
</div>
<div class="section-head">
<h2>Student services</h2>
</div>
<div class="links">
<a href="index.php?page=student/profile">👤 Profile</a>
<a href="index.php?page=student/attendance">🗓️ Attendance</a>
<a href="index.php?page=student/results">📈 Test Results</a>
<a href="index.php?page=student/notices">📢 Notices</a>
<a href="index.php?page=student/teacher-availability">👨‍🏫 Teacher Availability</a>
<a href="index.php?page=student/online">💻 Online / Offline</a>
<a href="index.php?page=student/leave">📝 Leave Request</a>
<a href="index.php?page=messages">✉️ Messaging</a>
</div>
