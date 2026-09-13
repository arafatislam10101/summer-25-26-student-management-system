<h1>My Attendance</h1>
<div class="card">
<form method="get" class="gridform">
<input type="hidden" name="page" value="student/attendance">
<input type="date" name="from" value="<?=e($from)?>">
<input type="date" name="to" value="<?=e($to)?>">
<button>Filter</button>
</form>
</div>
<div class="grid">
<div class="card">
<b>Total:</b>
<?=e($summary['total'])?>
</div>
<div class="card">
<b>Present:</b>
<?=e($summary['present'])?>
</div>
<div class="card">
<b>Late:</b>
<?=e($summary['late'])?>
</div>
<div class="card">
<b>Percentage:</b>
<?=e($summary['percentage'])?>%</div>
</div>
<div class="card">
<table>
<tr>
<th>Date</th>
<th>Status</th>
</tr>
<?php foreach($records as $r):?>

<tr>
<td>
<?=e($r['date'])?>
</td>
<td>
<?=e($r['status'])?>
</td>
</tr>
<?php endforeach;?>

</table>
</div>
