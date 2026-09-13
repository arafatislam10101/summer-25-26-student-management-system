<h1>My Profile</h1>

<div class="profile-page">

<section class="profile-cover">

<img src="assets/images/profiles/student.svg" alt="Student profile photo">

<h2>
<?=e($student['name'])?>
</h2>

<p>
<?=e($student['student_id'])?>
</p>

<span class="badge success">
Student Account
</span>

</section>



<section class="card">

<div class="section-head">

<h2>
Personal & academic information
</h2>

<div>

<span class="badge">
Profile
</span>


<a 
href="index.php?page=student/edit-profile" class="btn">
Edit Profile
</a>

</div>

</div>



<div class="profile-info">


<div class="info-item">

<span>
Full Name
</span>

<strong>
<?=e($student['name'])?>
</strong>

</div>



<div class="info-item">

<span>
Student ID
</span>

<strong>
<?=e($student['student_id'])?>
</strong>

</div>



<div class="info-item">

<span>
Email
</span>

<strong>
<?=e($student['email'])?>
</strong>

</div>



<div class="info-item">

<span>
Phone
</span>

<strong>
<?=e($student['phone'])?>
</strong>

</div>



<div class="info-item">

<span>
Class
</span>

<strong>
<?=e(($student['class_name']??'').' '.($student['section']??''))?>
</strong>

</div>



<div class="info-item">

<span>
Parent
</span>

<strong>
<?=e($student['parent_name'])?>
</strong>

</div>



<div class="info-item" style="grid-column:1/-1">

<span>
Address
</span>

<strong>
<?=e($student['address'])?>
</strong>

</div>


</div>

</section>

</div>