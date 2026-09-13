<h1>Manage Teachers</h1>
<?php $isEdit=!empty($edit);?>
<div class="card">
<h2>
<?=$isEdit?'Edit':'Add'?> Teacher</h2>
<form method="post" class="gridform">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<?php if($isEdit):?>

<input type="hidden" name="update" value="1">
<input type="hidden" name="id" value="<?=$edit['id']?>">
<input type="hidden" name="user_id" value="<?=$edit['user_id']?>">
<?php endif;?>

<input name="name" value="<?=e($edit['name']??'')?>" placeholder="Full name" required>
<input name="teacher_id" value="<?=e($edit['teacher_id']??'')?>" placeholder="Teacher ID" required>
<input type="email" name="email" value="<?=e($edit['email']??'')?>" placeholder="Email" required>
<?php if(!$isEdit):?>

<input type="password" name="password" minlength="8" placeholder="Password (min 8)" required>
<?php endif;?>

<input name="phone" value="<?=e($edit['phone']??'')?>" placeholder="Phone">
<input name="subject" value="<?=e($edit['subject']??'')?>" placeholder="Subject">
<div class="full-width admin-background-field">
<label><strong>Teacher Background</strong></label>
<textarea name="background" rows="5" placeholder="Education, qualifications, teaching experience, specialization, achievements..."><?=e($edit['background']??'')?></textarea>
<small class="muted">Add the teacher's education, qualifications, experience, specialization and other professional background.</small>
</div>
<button>
<?=$isEdit?'Update':'Add'?> Teacher</button>
</form>
<?php if($isEdit):?>

<a href="index.php?page=admin/teachers">Cancel</a>
<?php endif;?>

</div>
<div class="card">
<div class="tablewrap">
<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Subject</th>
<th>Teacher Background</th>
<th>Action</th>
</tr>
<?php foreach($teachers as $t):?>

<tr>
<td>
<?=e($t['teacher_id'])?>
</td>
<td>
<?=e($t['name'])?>
</td>
<td>
<?=e($t['email'])?>
</td>
<td>
<?=e($t['subject'])?>
</td>
<td class="teacher-background-cell">
<?php if(trim($t['background']??'')): ?>
<div><?=nl2br(e($t['background']))?></div>
<?php else: ?>
<span class="muted">No background added</span>
<?php endif; ?>
</td>
<td>
<a href="index.php?page=admin/teachers&edit=<?=$t['id']?>">Edit</a>
<button class="danger" data-ajax-action="delete_teacher" data-id="<?=$t['id']?>" data-confirm="Delete this teacher?">Delete</button>
</td>
</tr>
<?php endforeach;?>

</table>
</div>
</div>
