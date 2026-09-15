<h1>Messaging</h1>
<div class="card">
<form method="post" action="index.php?page=messages/send">
<?php csrf_field(); ?>
<select name="receiver_id" required>
<option value="">Select recipient</option>
<?php foreach ($users as $user): ?>

<option value="<?= e($user['id']) ?>">
<?= e($user['name'] . ' (' . $user['role'] . ')') ?>
</option>
<?php endforeach; ?>

</select>
<textarea
            name="message"
            placeholder="Message"
            required
        >
</textarea>
<button type="submit">Send Message</button>
</form>
</div>
<?php foreach ($messages as $message): ?>

<article class="card">
<b>From: <?= e($message['sender_name']) ?>
</b>
<p>
<?= nl2br(e($message['message'])) ?>
</p>
<small>
<?= e($message['created_at']) ?>
</small>
</article>
<?php endforeach; ?>

