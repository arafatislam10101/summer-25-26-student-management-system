<h1>Enter / Update Student Results</h1>
<div class="card">
<form method="get" class="gridform">
<input type="hidden" name="page" value="teacher/marks">
<select name="class_id" onchange="this.form.submit()" required>
<option value="0">Select class</option>
<?php foreach($classes as $c):?>

<option value="<?=$c['id']?>" <?=$cid===$c['id']?'selected':''?>>
<?=e($c['class_name'].' - '.$c['section'])?>
</option>
<?php endforeach;?>

</select>
</form>
</div>
<?php if($cid):?>

<div class="card">
<h2>Add Subject</h2>
<form method="post" class="gridform">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<input type="hidden" name="class_id" value="<?=$cid?>">
<input type="hidden" name="action" value="add_subject">
<input name="subject_name" maxlength="100" placeholder="Subject name e.g. Physics" required>
<button type="submit">Add Subject</button>
</form>
<p class="muted">Add a subject to this assigned class. It will immediately appear in the Subject list below for entering or updating marks.</p>
</div>
<?php endif;?>

<?php if($students):?>

<div class="card">
<h2>Add Result</h2>
<form method="post" class="gridform">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<input type="hidden" name="class_id" value="<?=$cid?>">
<select name="student_id" required>
<option value="">Select student</option>
<?php foreach($students as $s):?>

<option value="<?=$s['id']?>">
<?=e($s['student_id'].' - '.$s['name'])?>
</option>
<?php endforeach;?>

</select>
<select name="subject_id" required>
<option value="">Select subject</option>
<?php foreach($subjects as $sub):?>

<option value="<?=$sub['id']?>">
<?=e($sub['subject_name'])?>
</option>
<?php endforeach;?>

</select>
<input name="exam" placeholder="Exam e.g. Mid Term" required>
<input type="number" step="0.01" min="0" max="100" name="marks" placeholder="Marks (0-100)" required>
<button type="submit">Save Result</button>
</form>
<p class="muted">To change an existing result, use the Update button below.</p>
</div>
<?php endif;?>

<?php if($records):?>

<div class="card">
<div class="section-head">
<h2>Student Results</h2>
<span class="badge success">
<?=count($records)?> result<?=count($records)!==1?'s':''?>
</span>
</div>
<div style="overflow-x:auto">
<table>
<tr>
<th>Student</th>
<th>Subject</th>
<th>Exam</th>
<th>Marks</th>
<th>Action</th>
</tr>
<?php foreach($records as $r):?>

<tr>
<form method="post">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<input type="hidden" name="class_id" value="<?=$cid?>">
<input type="hidden" name="mark_id" value="<?=$r['id']?>">
<td>
<?=e($r['student_id'].' - '.$r['name'])?>
<input type="hidden" name="student_id" value="<?=$r['student_pk']?>">
</td>
<td>
<select name="subject_id" required>
<?php foreach($subjects as $sub):?>

<option value="<?=$sub['id']?>" <?=$sub['subject_name']===$r['subject_name']?'selected':''?>>
<?=e($sub['subject_name'])?>
</option>
<?php endforeach;?>

</select>
</td>
<td>
<input name="exam" value="<?=e($r['exam'])?>" required>
</td>
<td>
<input type="number" step="0.01" min="0" max="100" name="marks" value="<?=e($r['marks'])?>" required style="max-width:110px">
</td>
<td>
<button type="submit">Update</button>
</td>
</form>
</tr>
<?php endforeach;?>

</table>
</div>
</div>
<?php elseif($cid):?>
<div class="card">
<div class="empty">No results have been entered for this class yet.</div>
</div>
<?php endif;?>

