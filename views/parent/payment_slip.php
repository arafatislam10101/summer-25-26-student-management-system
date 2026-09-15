<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Slip #<?=e($payment['id'])?>
</title>
<style>
    *{box-sizing:border-box}
    body{margin:0;background:#f3f4f6;font-family:Arial,sans-serif;color:#222}
    .slip{width:800px;max-width:92%;margin:40px auto;background:#fff;padding:35px;border:1px solid #ddd}
    .head{text-align:center;border-bottom:2px solid #222;padding-bottom:18px;margin-bottom:25px}
    .head h1{margin:0 0 6px;font-size:28px}.head p{margin:0;color:#666}
    .title{text-align:center;font-size:22px;font-weight:bold;margin:20px 0}
    .info{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:25px}
    .info div{border:1px solid #ddd;padding:12px}.label{display:block;color:#777;font-size:13px;margin-bottom:5px}
    .amount{font-size:25px;font-weight:bold}
    .status{font-weight:bold}
    .actions{text-align:center;margin:25px 0 0}
    button{padding:10px 20px;border:0;background:#222;color:#fff;cursor:pointer;border-radius:5px}
    .note{margin-top:30px;font-size:12px;color:#777;text-align:center}
    @media print{
      body{background:#fff}.slip{width:100%;max-width:none;margin:0;border:0;padding:20px}
      .actions{display:none}
    }
  </style>
</head>
<body>
<div class="slip">
<div class="head">
<h1>EduManage</h1>
<p>Student Management System</p>
</div>
<div class="title">PAYMENT SLIP</div>
<div class="info">
<div>
<span class="label">Receipt No.</span>
<strong>#<?=e($payment['id'])?>
</strong>
</div>
<div>
<span class="label">Payment Date</span>
<strong>
<?=e($payment['paid_at'] ?? '—')?>
</strong>
</div>
<div>
<span class="label">Student Name</span>
<strong>
<?=e($payment['student_name'])?>
</strong>
</div>
<div>
<span class="label">Student ID</span>
<strong>
<?=e($payment['student_id'])?>
</strong>
</div>
<div>
<span class="label">Class</span>
<strong>
<?=e(trim(($payment['class_name'] ?? '').' '.($payment['section'] ?? '')) ?: '—')?>
</strong>
</div>
<div>
<span class="label">Purpose</span>
<strong>
<?=e($payment['purpose'])?>
</strong>
</div>
<div>
<span class="label">Payment Method</span>
<strong>
<?=e($payment['payment_method'] ?? '—')?>
</strong>
</div>
<div>
<span class="label">Transaction ID</span>
<strong>
<?=e($payment['transaction_id'] ?? '—')?>
</strong>
</div>
</div>
<div class="info">
<div>
<span class="label">Amount</span>
<span class="amount">৳ <?=e(number_format((float)$payment['amount'],2))?>
</span>
</div>
<div>
<span class="label">Status</span>
<span class="status">
<?=e($payment['status'])?>
</span>
</div>
</div>
<div class="actions">
<button onclick="window.print()">🖨️ Print Payment Slip</button>
</div>
<div class="note">This is a computer-generated payment slip.</div>
</div>
</body>
</html>
