<h1>Child's Attendance</h1>
<div class="grid">
<div class="card">
<b>Attendance:</b>
<?=e($summary['percentage'])?>%</div>
<div class="card">
<b>Present:</b>
<?=e($summary['present'])?>
</div>
<div class="card">
<b>Absent:</b>
<?=e($summary['absent'])?>
</div>
<div class="card">
<b>Late:</b>
<?=e($summary['late'])?>
</div>
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
