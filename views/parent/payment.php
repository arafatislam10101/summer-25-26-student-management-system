<div class="page-heading">
<h1>Payment System / Accounts</h1>
<p>Make a payment for your child and print the payment slip after successful payment.</p>
</div>
<div class="card">
<div class="section-head">
<h2>💳 Make Payment</h2>
<span class="badge success">Secure Demo Payment</span>
</div>
<form method="post" action="index.php?page=parent/make-payment">
<input type="hidden" name="csrf" value="<?=e(csrf())?>">
<div class="grid">
<div>
<label>Amount (৳)</label>
<input type="number" name="amount" min="1" max="1000000" step="0.01" required placeholder="Enter amount">
</div>
<div>
<label>Purpose</label>
<input type="text" name="purpose" maxlength="100" required placeholder="e.g. Monthly Fee">
</div>
<div>
<label>Payment Method</label>
<select name="payment_method" required>
<option value="">Select method</option>
<option>bKash</option>
<option>Nagad</option>
<option>Card</option>
<option>Bank Transfer</option>
</select>
</div>
<div>
<label>Transaction ID</label>
<input type="text" name="transaction_id" maxlength="100" placeholder="Required for bKash/Nagad/Bank Transfer">
</div>
</div>
<p class="muted">For this academic project, submitting the form records the payment as completed. Use a real payment gateway only if one is configured.</p>
<button class="btn btn-primary" type="submit">💳 Make Payment</button>
</form>
</div>
<div class="card">
<div class="section-head">
<h2>Payment History</h2>
</div>
<?php if(empty($payments)): ?>

<p class="muted">No payment records found.</p>
<?php else: ?>
<div class="table-wrap">
<table>
<tr>
<th>Purpose</th>
<th>Amount</th>
<th>Method</th>
<th>Transaction ID</th>
<th>Status</th>
<th>Paid At</th>
<th>Action</th>
</tr>
<?php foreach($payments as $p): ?>

<tr>
<td>
<?=e($p['purpose'])?>
</td>
<td>৳ <?=e(number_format((float)$p['amount'],2))?>
</td>
<td>
<?=e($p['payment_method'] ?? '—')?>
</td>
<td>
<?=e($p['transaction_id'] ?? '—')?>
</td>
<td>
<span class="badge <?=$p['status']==='Paid'?'success':'warning'?>">
<?=e($p['status'])?>
</span>
</td>
<td>
<?=e($p['paid_at'] ?? '—')?>
</td>
<td>
<a class="btn btn-primary" target="_blank" href="index.php?page=parent/payment-slip&id=<?=e($p['id'])?>">🖨️ Print Slip</a>
</td>
</tr>
<?php endforeach; ?>

</table>
</div>
<?php endif; ?>

</div>
