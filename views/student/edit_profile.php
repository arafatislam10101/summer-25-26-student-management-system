<h1>Edit Profile</h1>

<div class="edit-profile-page">

<section class="card">

<div class="section-head">
<h2>Edit Personal Information</h2>
<span class="badge">Update</span>
</div>


<form method="POST" action="index.php?page=student/update-profile">


<?=csrf_field()?>


<div class="edit-profile-form">


<div class="info-item">
<span>Full Name</span>

<input 
type="text" 
name="name"
value="<?=e($student['name'])?>"
required>
</div>



<div class="info-item">
<span>Email</span>

<input 
type="email" 
name="email"
value="<?=e($student['email'])?>"
required>
</div>



<div class="info-item">
<span>Phone</span>

<input 
type="text" 
name="phone"
value="<?=e($student['phone'])?>">
</div>



<div class="info-item" style="grid-column:1/-1">

<span>Address</span>

<textarea 
name="address"
rows="4"><?=e($student['address'])?></textarea>

</div>


</div>



<button type="submit" class="btn">
Save Changes
</button>


</form>


</section>

</div>