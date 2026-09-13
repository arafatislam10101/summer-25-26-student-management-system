<h1>Teacher Background</h1>
<div class="card">
<form method="post">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<textarea name="background" placeholder="Education, experience, qualifications, specialization..." required>
<?=e($t['background']??'')?>
</textarea>
<button>Save Background</button>
</form>
</div>
