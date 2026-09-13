<h1>Request Management</h1>
<div class="card">
<table>
<tr>
<th>Type</th>
<th>Student</th>
<th>Details</th>
<th>Status</th>
<th>Action</th>
</tr>
<?php foreach($requests as $r):?>

<tr>
<td>
<?=e($r['type'])?>
</td>
<td>
<?=e($r['name'])?>
</td>
<td>
<?=e($r['details'])?>
</td>
<td>
<?=e($r['status'])?>
</td>
<td>
<?php if($r['status']==='Pending'):?>

<form method="post" class="inline">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<input type="hidden" name="type" value="<?=e($r['type'])?>">
<input type="hidden" name="id" value="<?=$r['request_id']?>">
<button name="status" value="Approved">Approve</button>
<button name="status" value="Rejected" class="danger">Reject</button>
</form>
<?php endif;?>

</td>
</tr>
<?php endforeach;?>

</table>
</div>
