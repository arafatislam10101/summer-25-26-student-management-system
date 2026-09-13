<h1>Manage Classes & Subjects</h1>

<div class="card">
<h2>Add / Update Class + Subject</h2>
<p class="muted">Create a class and its subject together. When updating a class, you can update the selected subject at the same time.</p>
<form method="post">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<input type="hidden" name="entity" value="class">
<input type="hidden" name="id" value="<?=e($editClass['id']??0)?>">

<div class="grid">
<div>
<label>Class</label>
<input name="name" value="<?=e($editClass['class_name']??'')?>" placeholder="Class name" required>
</div>
<div>
<label>Section</label>
<input name="section" value="<?=e($editClass['section']??'')?>" placeholder="Section" required>
</div>
<div>
<label>Teacher</label>
<select name="teacher_id">
<option value="0">Unassigned</option>
<?php $selectedTeacher=(int)($editClass['teacher_id']??0); ?>
<?php foreach($teachers as $t):?>
<option value="<?=$t['id']?>" <?=((int)$t['id']===$selectedTeacher)?'selected':''?>><?=e($t['name'])?></option>
<?php endforeach;?>
</select>
<small class="muted">One teacher per class. The same teacher may teach multiple classes.</small>
</div>
<div>
<label>Subject</label>
<?php
$editSubjectId=(int)($editSubjectForClass['id']??0);
$editSubjectName=$editSubjectForClass['subject_name']??'';
?>
<select name="subject_id">
<option value="0">Create new subject</option>
<?php foreach($subjectsForClass as $s):?>
<option value="<?=$s['id']?>" <?=((int)$s['id']===$editSubjectId)?'selected':''?>><?=e($s['subject_name'])?></option>
<?php endforeach;?>
</select>
<input name="subject_name" value="<?=e($editSubjectName)?>" placeholder="Subject name (e.g. Mathematics)" required>
<small class="muted">For a new class, enter the subject name. For an existing class, select the subject you want to update.</small>
</div>
</div>

<button>Save Class & Subject</button>
<?php if($editClass):?><a href="index.php?page=admin/classes">Cancel</a><?php endif;?>
</form>
</div>

<div class="card">
<h2>Classes</h2>
<div class="tablewrap">
<table>
<tr><th>Class</th><th>Section</th><th>Teacher</th><th>Subjects</th><th>Action</th></tr>
<?php foreach($classes as $c):?>
<tr>
<td><?=e($c['class_name'])?></td>
<td><?=e($c['section'])?></td>
<td><?=e($c['teacher_name'])?></td>
<td>
<?php
$names=[];
foreach($subjects as $s) if((int)($s['class_id']??0)===(int)$c['id']) $names[]=$s['subject_name'];
echo e($names ? implode(', ', $names) : '—');
?>
</td>
<td>
<a href="index.php?page=admin/classes&edit_class=<?=$c['id']?>">Edit</a>
<button class="danger" data-ajax-action="delete_class" data-id="<?=$c['id']?>" data-confirm="Delete class? Subjects linked to it will also be deleted.">Delete</button>
</td>
</tr>
<?php endforeach;?>
</table>
</div>
</div>

<div class="card">
<h2>Subjects</h2>
<table>
<tr><th>Subject</th><th>Class</th><th>Action</th></tr>
<?php foreach($subjects as $s):?>
<tr>
<td><?=e($s['subject_name'])?></td>
<td><?=e(($s['class_name']??'General').' '.($s['section']??''))?></td>
<td>
<a href="index.php?page=admin/classes&edit_class=<?=$s['class_id']??0?>">Edit with Class</a>
<button class="danger" data-ajax-action="delete_subject" data-id="<?=$s['id']?>" data-confirm="Delete subject?">Delete</button>
</td>
</tr>
<?php endforeach;?>
</table>
</div>
