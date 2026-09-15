<h1>Availability</h1>
<div class="card">
<form method="post" class="gridform">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<select name="day">
<option>Monday</option>
<option>Tuesday</option>
<option>Wednesday</option>
<option>Thursday</option>
<option>Friday</option>
<option>Saturday</option>
<option>Sunday</option>
</select>
<input type="time" name="from_time" required>
<input type="time" name="to_time" required>
<button>Save Availability</button>
</form>
</div>
<div class="card">
<table>
<tr>
<th>Day</th>
<th>From</th>
<th>To</th>
<th>Action</th>
</tr>
<?php foreach($availability as $a):?>

<tr>
<td>
<?=e($a['day'])?>
</td>
<td>
<?=e($a['from_time'])?>
</td>
<td>
<?=e($a['to_time'])?>
</td>
<td>
<button class="danger" data-ajax-action="delete_availability" data-id="<?=$a['id']?>">Delete</button>
</td>
</tr>
<?php endforeach;?>

</table>
</div>
