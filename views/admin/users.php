<h1>User Accounts</h1>
<div class="card">
<table>
<tr>
<th>Name</th>
<th>Email</th>
<th>Role</th>
<th>Status</th>
<th>Action</th>
</tr>
<?php foreach($users as $u):?>

<tr>
<td>
<?=e($u['name'])?>
</td>
<td>
<?=e($u['email'])?>
</td>
<td>
<?=e($u['role'])?>
</td>
<td>
<span class="badge">
<?=e($u['status'])?>
</span>
</td>
<td>
<?php if($u['role']!=='admin'):?>

<button data-ajax-action="toggle_user" data-id="<?=$u['id']?>">Toggle Status</button>
<?php else:?>Protected<?php endif;?>

</td>
</tr>
<?php endforeach;?>

</table>
</div>
