<h1>Notices & Accounts</h1>
<div class="card">
<p>
<b>Child:</b>
<?=e($child['student_name'])?>
</p>
<p>
<b>Student Account:</b>
<?=e($child['student_email'])?>
</p>
</div>
<?php foreach($notices as $n):?>

<div class="card">
<h2>
<?=e($n['title'])?>
</h2>
<p>
<?=nl2br(e($n['description']))?>
</p>
</div>
<?php endforeach;?>

