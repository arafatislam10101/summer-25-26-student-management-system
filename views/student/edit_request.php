<h1>
Edit Request
</h1>


<div class="card">


<form method="post" action="index.php?page=student/update-request">


<?=csrf_field()?>


<input 
type="hidden" 
name="id" 
value="<?=e($request['id'])?>"
>


<input 
type="hidden" 
name="type" 
value="<?=e($type)?>"
>



<?php if(str_starts_with($type,'Online/')): ?>


<label>

Request Type

<input 
type="text"
value="<?=e($request['request_type']??$type)?>"
readonly
>

</label>


<?php else: ?>


<label>

From Date

<input 
type="date"
name="from_date"
value="<?=e($request['from_date'])?>"
required
>

</label>



<label>

To Date

<input 
type="date"
name="to_date"
value="<?=e($request['to_date'])?>"
required
>

</label>


<?php endif; ?>



<label>

Reason

<textarea 
name="reason"
required><?=e($request['reason'])?></textarea>

</label>



<button type="submit" class="btn">

Save Changes

</button>


</form>


</div>