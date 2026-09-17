<div class="section-head">
<h1>Teacher Availability</h1>
<span class="badge">Check when teachers are available</span>
</div>
<div class="card">
<p class="muted">Students can view the available days and times set by active teachers.</p>
<?php if(!$availability): ?>

<p>No teacher availability has been added yet.</p>
<?php else: ?>
<div style="overflow-x:auto">
<table>
<tr>
<th>Teacher</th>
<th>Teacher ID</th>
<th>Subject</th>
<th>Day</th>
<th>Available From</th>
<th>Available To</th>
</tr>
<?php foreach($availability as $a): ?>

<tr>
<td>
<?=e($a['teacher_name'])?>
</td>
<td>
<?=e($a['teacher_id'])?>
</td>
<td>
<?=e($a['subject']?:'Not specified')?>
</td>
<td>
<?=e($a['day'])?>
</td>
<td>
<?=e(date('h:i A',strtotime($a['from_time'])))?>
</td>
<td>
<?=e(date('h:i A',strtotime($a['to_time'])))?>
</td>
</tr>
<?php endforeach; ?>

</table>
</div>
<?php endif; ?>

</div>
