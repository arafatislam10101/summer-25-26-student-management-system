<div class="hero">
<img class="hero-profile" src="assets/images/profiles/admin.svg" alt="Admin profile photo">
<div class="hero-content">
<h1>Good to see you, <?=e($_SESSION['user']['name']??'Admin')?> 👋</h1>
<p>Manage the whole academic environment from one clean, centralized workspace.</p>
</div>
</div>
<div class="grid">
<?php $icons=['students'=>'🎓','teachers'=>'👨‍🏫','classes'=>'🏫','notices'=>'📢','leave_requests'=>'📝','online_requests'=>'💻','package_requests'=>'📦','messages'=>'✉️']; foreach($stats as $k=>$v):?>
<div class="card kpi">
<div>
<div class="muted">
<?=e(ucwords(str_replace('_',' ',$k)))?>
</div>
<div class="stat">
<?=e($v)?>
</div>
</div>
<div class="kpi-icon">
<?=e($icons[$k]??'•')?>
</div>
</div>
<?php endforeach;?>

</div>
<div class="section-head">
<h2>Quick actions</h2>
<small>Everything you need is one click away</small>
</div>
<div class="links">
<a href="index.php?page=admin/students">🎓 Students</a>
<a href="index.php?page=admin/teachers">👨‍🏫 Teachers</a>
<a href="index.php?page=admin/classes">📚 Classes & Subjects</a>
<a href="index.php?page=admin/notices">📢 Notices</a>
<a href="index.php?page=admin/users">👥 User Accounts</a>
<a href="index.php?page=admin/requests">📝 Requests</a>
<a href="index.php?page=admin/at-risk">⚠️ At-Risk</a>
<a href="index.php?page=admin/feedback">💬 Feedback</a>
<a href="index.php?page=messages">✉️ Messaging</a>
</div>
