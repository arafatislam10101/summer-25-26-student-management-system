<h1>Rating & Feedback</h1>
<div class="card">
<form method="post">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<select name="rating">
<option value="5">5 - Excellent</option>
<option value="4">4 - Good</option>
<option value="3">3 - Average</option>
<option value="2">2 - Poor</option>
<option value="1">1 - Very Poor</option>
</select>
<textarea name="comment" placeholder="Feedback">
</textarea>
<button>Submit Feedback</button>
</form>
</div>
<div class="card">
<h2>Previous Feedback</h2>
<table>
<tr>
<th>Rating</th>
<th>Comment</th>
<th>Date</th>
</tr>
<?php foreach($feedback as $f):?>

<tr>
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
