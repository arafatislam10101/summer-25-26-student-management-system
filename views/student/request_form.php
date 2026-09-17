<h1>
<?= $kind==='leave'?'Leave Request':'Online / Offline Request' ?>
</h1>
<div class="card">
<form method="post">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<?php if($kind==='leave'):?>

<label>From<input type="date" name="from_date" required>
</label>
<label>To<input type="date" name="to_date" required>
</label>
<?php else:?>
<select name="request_type">
<option>Online</option>
<option>Offline</option>
</select>
<?php endif;?>

<textarea name="reason" placeholder="Reason" required>
</textarea>
<button>Submit Request</button>
</form>
</div>
<div class="card">
<h2>My Requests</h2>
<table>

<tr>
<th>Type</th>
<th>Reason</th>
<th>Status</th>
<th>Action</th>
</tr>


<?php foreach($requests as $r): ?>

<tr>

<td>
<?=e($r['type'])?>
</td>


<td>
<?=e($r['reason'])?>
</td>


<td>
<?=e($r['status'])?>
</td>


<td>

<a 
href="index.php?page=student/edit-request&id=<?=$r['id']?>&type=<?=e($r['type'])?>" 
class="btn">
Edit
</a>


<a 
href="index.php?page=student/delete-request&id=<?=$r['id']?>&type=<?=e($r['type'])?>" 
class="btn danger"
onclick="return confirm('Delete this request?')">
Delete
</a>


</td>

</tr>

<?php endforeach; ?>


</table>
</div>
