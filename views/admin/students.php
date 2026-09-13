<h1>Manage Students</h1>
<?php $isEdit=!empty($edit);?>
<div class="card">
<h2>
<?=$isEdit?'Edit':'Add'?> Student</h2>
<form method="post" class="gridform">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<?php if($isEdit):?>

<input type="hidden" name="update" value="1">
<input type="hidden" name="id" value="<?=$edit['id']?>">
<input type="hidden" name="user_id" value="<?=$edit['user_id']?>">
<?php endif;?>

<input name="name" value="<?=e($edit['name']??'')?>" placeholder="Full name" required>
<input name="student_id" value="<?=e($edit['student_id']??'')?>" placeholder="Student ID" required>
<input type="email" name="email" value="<?=e($edit['email']??'')?>" placeholder="Email" required>
<?php if(!$isEdit):?>

<input type="password" name="password" placeholder="Password (min 8)" minlength="8" required>
<?php endif;?>

<input name="phone" value="<?=e($edit['phone']??'')?>" placeholder="Phone">
<input name="address" value="<?=e($edit['address']??'')?>" placeholder="Address">
<select name="class_id">
<option value="0">Unassigned</option>
<?php foreach($classes as $c):?>

<option value="<?=$c['id']?>" <?=((int)($edit['class_id']??0)==$c['id'])?'selected':''?>>
<?=e($c['class_name'].' - '.$c['section'])?>
</option>
<?php endforeach;?>

</select>
<select name="parent_id">
<option value="0">No parent</option>
<?php foreach($parents as $p):?>

<option value="<?=$p['id']?>" <?=((int)($edit['parent_id']??0)==$p['id'])?'selected':''?>>
<?=e($p['name'])?>
</option>
<?php endforeach;?>

</select>
<button>
<?=$isEdit?'Update':'Add'?> Student</button>
</form>
<?php if($isEdit):?>

<a href="index.php?page=admin/students">Cancel</a>
<?php endif;?>

</div>
<div class="card">
<input id="tableSearch" placeholder="Search students...">
<div class="tablewrap">
<table id="dataTable">
<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Class</th>
<th>Parent</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php foreach($students as $s):?>

<tr>
<td>
<?=e($s['student_id'])?>
</td>
<td>
<?=e($s['name'])?>
</td>
<td>
<?=e($s['email'])?>
</td>
<td>
<?=e(($s['class_name']??'').' '.($s['section']??''))?>
</td>
<td>
<?=e($s['parent_name'])?>
</td>
<td>
<a href="index.php?page=admin/students&edit=<?=$s['id']?>">Edit</a>
<button class="danger" data-ajax-action="delete_student" data-id="<?=$s['id']?>" data-confirm="Delete this student?">Delete</button>
</td>
</tr>
<?php endforeach;?>

</tbody>
</table>
</div>
</div>
