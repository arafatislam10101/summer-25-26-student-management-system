<h1>Feedback</h1>
<div class="card">
<table>
<tr>
<th>Student</th>
<th>Rating</th>
<th>Comment</th>
<th>Date</th>
</tr>
<?php foreach($feedback as $f):?>

<tr>
<td>
<?=e($f['student_name'].' ('.$f['student_id'].')')?>
</td>
<td>
<?=e($f['rating'])?> / 5</td>
<td>
<?=e($f['comment'])?>
</td>
<td>
<?=e($f['created_at'])?>
</td>
</tr>
<?php endforeach;?>

</table>
</div>
