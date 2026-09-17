<h1>Notices</h1>
<?php foreach($notices as $n):?>

<div class="card">
<h2>
<?=e($n['title'])?>
</h2>
<p>
<?=nl2br(e($n['description']))?>
</p>
<small>
<?=e($n['created_at'])?>
</small>
</div>
<?php endforeach;?>

