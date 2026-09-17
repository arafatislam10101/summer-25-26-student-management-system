<h1>
<?=e($title??'Results')?>
</h1>
<div class="card">
<table>
<tr>
<th>Subject</th>
<th>Exam</th>
<th>Marks</th>
<th>Grade</th>
</tr>
<?php foreach($records as $r):$m=(float)$r['marks'];$g=$m>=80?'A+':($m>=70?'A':($m>=60?'B':($m>=50?'C':'F')));?>
<tr>
<td>
<?=e($r['subject_name'])?>
</td>
<td>
<?=e($r['exam'])?>
</td>
<td>
<?=e($r['marks'])?>
</td>
<td>
<?=e($g)?>
</td>
</tr>
<?php endforeach;?>

</table>
</div>
