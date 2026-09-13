<div class="hero">
<img class="hero-profile" src="assets/images/profiles/teacher.svg" alt="Teacher profile photo">
<div class="hero-content">
<h1>Hello, <?=e($_SESSION['user']['name']??'Teacher')?> 👋</h1>
<p>Track your assigned classes, attendance, marks and availability from your teaching portal.</p>
</div>
</div>
<div class="card">
<div class="section-head">
<h2>My Classes</h2>
<span class="badge success">
<?=count($classes)?> class<?=count($classes)!==1?'es':''?>
</span>
</div>
<?php if($classes): ?>

<form class="gridform" onsubmit="return false;">
<label>
<strong>Select Class</strong>
<select id="teacherClassSelect" required>
<option value="">Select a class</option>
<?php foreach($classes as $c): ?>

<option value="<?=$c['id']?>">
<?=e($c['class_name'].' - '.$c['section'])?>
</option>
<?php endforeach; ?>

</select>
</label>
<div class="links" style="align-self:end">
<a id="attendanceLink" href="index.php?page=teacher/attendance">🗓️ Attendance</a>
<a id="marksLink" href="index.php?page=teacher/marks">📝 Marks</a>
</div>
</form>
<p class="muted">Select any class assigned to you. A teacher can handle multiple classes.</p>
<?php else: ?>
<div class="empty">No assigned classes yet.</div>
<?php endif; ?>

</div>
<div class="section-head">
<h2>Teaching Tools</h2>
</div>
<div class="links">
<a href="index.php?page=teacher/attendance">🗓️ Attendance / Range</a>
<a href="index.php?page=teacher/marks">📝 Enter / Update Marks</a>
<a href="index.php?page=teacher/availability">⏰ Availability</a>
<a href="index.php?page=teacher/background">👨‍🏫 Teacher Background</a>
<a href="index.php?page=messages">✉️ Messaging</a>
</div>
<script>
(function(){
  const s=document.getElementById('teacherClassSelect');
  if(!s)return;
  const a=document.getElementById('attendanceLink'),m=document.getElementById('marksLink');
  s.addEventListener('change',function(){
    const id=this.value;
    a.href='index.php?page=teacher/attendance'+(id?'&class_id='+encodeURIComponent(id):'');
    m.href='index.php?page=teacher/marks'+(id?'&class_id='+encodeURIComponent(id):'');
  });
})();
</script>
