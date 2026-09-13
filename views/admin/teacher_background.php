<h1>Teacher Background</h1>
<div class="card">
<h2>Update Teacher Background</h2>
<form method="get" class="gridform">
<input type="hidden" name="page" value="admin/teacher-background">
<select name="edit" onchange="this.form.submit()" required>
<option value="">Select Teacher</option>
<?php foreach($teachers as $t): ?>
<option value="<?=$t['id']?>" <?=($edit && (int)$edit['id']===(int)$t['id'])?'selected':''?>><?=e($t['name'])?> — <?=e($t['teacher_id'])?></option>
<?php endforeach; ?>
</select>
<noscript><button>Load Teacher</button></noscript>
</form>
</div>
<?php if($edit): ?>
<div class="card">
<h2><?=e($edit['name'])?></h2>
<p class="muted">Teacher ID: <?=e($edit['teacher_id'])?> · Subject: <?=e($edit['subject']??'')?></p>
<form method="post">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<input type="hidden" name="teacher_id" value="<?=$edit['id']?>">
<label><strong>Professional Background</strong></label>
<textarea name="background" rows="12" placeholder="Education, qualifications, teaching experience, specialization, achievements and other professional information..."><?=e($edit['background']??'')?></textarea>
<button>Save Background</button>
</form>
</div>
<?php endif; ?>
