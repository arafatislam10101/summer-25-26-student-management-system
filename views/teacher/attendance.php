<h1>Attendance & Range</h1>
<div class="card">
<form method="get" class="gridform">
<input type="hidden" name="page" value="teacher/attendance">
<label>Class<select name="class_id" required onchange="this.form.submit()">
<option value="0">Select class</option>
<?php foreach($classes as $c):?>

<option value="<?=$c['id']?>" <?=$cid===$c['id']?'selected':''?>>
<?=e($c['class_name'].' - '.$c['section'])?>
</option>
<?php endforeach;?>

</select>
</label>
<label>Attendance Date<input type="date" name="date" value="<?=e($date)?>">
</label>
<label>From<input type="date" name="from" value="<?=e($from)?>">
</label>
<label>To<input type="date" name="to" value="<?=e($to)?>">
</label>
<button type="submit">Load</button>
</form>
</div>
<?php if($students):?>

<div class="card">
<h2>Take / Update Attendance</h2>
<p class="muted">Class: <strong>
<?=e(($classes[array_search($cid,array_column($classes,'id'))]['class_name']??'').' - '.($classes[array_search($cid,array_column($classes,'id'))]['section']??''))?>
</strong> &nbsp; Date: <strong>
<?=e($date)?>
</strong>
</p>
<form method="post">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<input type="hidden" name="class_id" value="<?=$cid?>">
<label>Date <input type="date" name="date" value="<?=e($date)?>" required>
</label>
<div style="overflow-x:auto">
<table>
<tr>
<th>Student</th>
<th>Status</th>
</tr>
<?php foreach($students as $s):?>

<tr>
<td>
<?=e($s['student_id'].' - '.$s['name'])?>
</td>
<td>
<select name="status[<?=$s['id']?>]">
<option value="Present" <?=($existing[(int)$s['id']]??'Present')==='Present'?'selected':''?>>Present</option>
<option value="Absent" <?=($existing[(int)$s['id']]??'Present')==='Absent'?'selected':''?>>Absent</option>
<option value="Late" <?=($existing[(int)$s['id']]??'Present')==='Late'?'selected':''?>>Late</option>
</select>
</td>
</tr>
<?php endforeach;?>

</table>
</div>
<button type="submit">Save / Update Attendance</button>
</form>
</div>
<?php elseif($cid):?>
<div class="card">
<div class="empty">No students are assigned to this class.</div>
</div>
<?php endif;?>

<?php if($records):?>

<div class="card">
<h2>Attendance Range Results</h2>
<div style="overflow-x:auto">
<table>
<tr>
<th>Date</th>
<th>Student</th>
<th>Status</th>
</tr>
<?php foreach($records as $r):?>

<tr>
<td>
<?=e($r['date'])?>
</td>
<td>
<?=e($r['student_id'].' - '.$r['name'])?>
</td>
<td>
<?=e($r['status'])?>
</td>
</tr>
<?php endforeach;?>

</table>
</div>
</div>
<?php endif;?>

