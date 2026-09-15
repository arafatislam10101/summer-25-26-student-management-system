<?php if ($navUser): ?>

</main>
<footer>EduManage Student Management System · <?=date('Y')?> · <?=esc(ucfirst($role))?> Portal</footer>
</section>
</div>
<?php endif; ?>

<script>window.CSRF=<?=json_encode(csrf())?>;</script>
<script src="assets/js/app.js">
</script>
</body>
</html>
