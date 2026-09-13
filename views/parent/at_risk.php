<div class="page-heading">
<h1>At-Risk Alerts</h1>
<p>Monitor your child's attendance and academic performance.</p>
</div>
<div class="card at-risk-card">
<?php
    $attendance=(float)($a['attendance_pct'] ?? 0);
    $average=(float)($m['average_marks'] ?? 0);
    $lowAttendance = $attendance < 60;
    $lowMarks = $average < 50;
    $isRisk = $lowAttendance || $lowMarks;
  ?>
<?php if($isRisk): ?>

<div class="status-row">
<div>
<h2>⚠️ Attention Required</h2>
<p class="muted">Your child currently needs academic attention.</p>
</div>
<span class="badge danger">At Risk</span>
</div>
<?php else: ?>
<div class="status-row">
<div>
<h2>✓ Good Standing</h2>
<p class="muted">No at-risk condition is currently detected.</p>
</div>
<span class="badge success">Good</span>
</div>
<?php endif; ?>

<div class="risk-stats">
<div class="risk-stat">
<span>Attendance</span>
<strong>
<?=e(number_format($attendance,2))?>%</strong>
<small>Minimum target: 60%</small>
</div>
<div class="risk-stat">
<span>Average Marks</span>
<strong>
<?=e(number_format($average,2))?>
</strong>
<small>Minimum target: 50</small>
</div>
</div>
<?php if($isRisk): ?>

<div class="alert warning">
<?php if($lowAttendance && $lowMarks): ?>

        ⚠️ Attendance is below 60% and average marks are below 50.
      <?php elseif($lowAttendance): ?>
        ⚠️ Attendance is below the minimum target of 60%.
      <?php else: ?>
        ⚠️ Average marks are below the minimum target of 50.
      <?php endif; ?>

      Please contact the teacher and take appropriate action.
    </div>
<?php else: ?>
<div class="alert success">✓ Your child is currently above both risk thresholds.</div>
<?php endif; ?>

</div>
