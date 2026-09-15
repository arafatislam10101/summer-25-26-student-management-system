<h1>Request for Package</h1>
<div class="card">
<form method="post">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<input name="package_name" placeholder="Package name" required>
<textarea name="reason" placeholder="Reason">
</textarea>
<button>Submit Request</button>
</form>
</div>
<div class="card">
<h2>Request History</h2>
<table>
<tr>
<th>Package</th>
<th>Reason</th>
<th>Status</th>
<th>Date</th>
</tr>
<?php foreach($requests as $r):?>

<tr>
<td>
<?=e($r['package_name'])?>
</td>
<td>
<?=e($r['reason'])?>
</td>
<td>
<?=e($r['status'])?>
</td>
<td>
<?=e($r['created_at'])?>
</td>
</tr>
<?php endforeach;?>

</table>
</div>
