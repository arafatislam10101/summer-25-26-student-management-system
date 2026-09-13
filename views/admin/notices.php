<h1>Notices</h1>
<div class="card">
<h2>
<?=!empty($edit)?'Edit':'Add'?> Notice</h2>
<form method="post">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<input type="hidden" name="id" value="<?=e($edit['id']??0)?>">
<input name="title" value="<?=e($edit['title']??'')?>" placeholder="Title" required>
<textarea name="description" required>
<?=e($edit['description']??'')?>
</textarea>
<button>Save Notice</button>
</form>
</div>
<div class="card">
<table>
<tr>
<th>Title</th>
<th>Description</th>
<th>Action</th>
</tr>
<?php foreach($notices as $n):?>

<tr>
<td>
<?=e($n['title'])?>
</td>
<td>
<?=e($n['description'])?>
</td>
<td>
<a href="index.php?page=admin/notices&edit=<?=$n['id']?>">Edit</a>
<button class="danger" data-ajax-action="delete_notice" data-id="<?=$n['id']?>" data-confirm="Delete notice?">Delete</button>
</td>
</tr>
<?php endforeach;?>

</table>
</div>
