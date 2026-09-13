<h1>At-Risk Students</h1>
<div class="card">
<table>
<tr>
<th>Student</th>
<th>Attendance %</th>
<th>Average Marks</th>
</tr>
<?php foreach($atRisk as $r):?>

<tr>
<td>
<?=e($r['student_id'].' - '.$r['name'])?>
</td>
<td>
<?=e($r['attendance_pct'])?>%</td>
<td>
<?=e($r['average_marks'])?>
</td>
</tr>
<?php endforeach;?>

</table>
</div>
